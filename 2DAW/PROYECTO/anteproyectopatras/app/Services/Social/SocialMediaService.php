<?php

namespace App\Services\Social;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\MarketAsset;
use App\Models\Report;
use App\Models\Repost;
use App\Models\Bookmark;
use App\Services\MarketDataService;
use App\Services\Storage\FileStorageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * SocialMediaService - Motor de interacciones sociales de Pipkonx.
 * 
 * Centraliza la lógica de grafos sociales, composición de feeds,
 * gestión de contenido y analíticas de la comunidad.
 */
class SocialMediaService
{
    protected $marketData;
    protected $storage;

    public function __construct(MarketDataService $marketData, FileStorageService $storage)
    {
        $this->marketData = $marketData;
        $this->storage = $storage;
    }

    /**
     * Recupera el Feed de noticias filtrado y paginado.
     */
    public function getFeedData(string $tab, ?int $userId, array $blockedIds)
    {
        $query = Post::with(['user', 'marketAsset', 'likes', 'comments' => function($q) use ($blockedIds) {
                $q->whereNull('parent_id')
                  ->whereNotIn('user_id', $blockedIds)
                  ->with(['user', 'likes', 'replies' => function($sq) use ($blockedIds) {
                      $sq->whereNotIn('user_id', $blockedIds)->with(['user', 'likes'])->latest();
                  }])->latest();
            }])
            ->withCount(['likes', 'comments', 'reposts'])
            ->whereNotIn('user_id', $blockedIds);

        // Aplicar algoritmos de filtrado
        if ($tab === 'following' && $userId) {
            $followingIds = DB::table('followers')->where('follower_id', $userId)->pluck('followed_id');
            $query->whereIn('user_id', $followingIds)->latest();
        } elseif ($tab === 'best') {
            $query->orderByRaw('(likes_count + comments_count + reposts_count) DESC')->latest();
        } else {
            $query->latest();
        }

        return $query->paginate(15)->through(fn($post) => $this->enrichPost($post, $userId));
    }

    /**
     * Recupera el muro de publicaciones de un usuario específico.
     */
    public function getWallData(int $profileUserId, ?int $viewerId, array $blockedIds)
    {
        $profileUser = User::find($profileUserId);
        $pinnedPostId = $profileUser ? $profileUser->pinned_post_id : null;

        $query = Post::with(['user', 'marketAsset', 'likes', 'comments.user'])
            ->withCount(['likes', 'comments', 'reposts'])
            ->where(function ($q) use ($profileUserId) {
                $q->where('user_id', $profileUserId)
                  ->orWhereHas('reposts', function($sq) use ($profileUserId) {
                      $sq->where('user_id', $profileUserId);
                  });
            })
            ->whereNotIn('user_id', $blockedIds);

        if ($pinnedPostId) {
            $query->orderByRaw("CASE WHEN id = ? THEN 1 ELSE 0 END DESC", [$pinnedPostId]);
        }

        return $query->latest()
            ->paginate(10)
            ->through(function($post) use ($viewerId, $profileUserId) {
                $enriched = $this->enrichPost($post, $viewerId, $profileUserId);
                // Si el post no es del dueño del muro, significa que es un repost en su muro
                $enriched->wall_is_repost = $post->user_id !== $profileUserId;
                return $enriched;
            });
    }

    /**
     * Alterna el estado de seguimiento entre dos usuarios.
     */
    public function toggleFollow(int $followerId, int $followedId): array
    {
        $follower = User::find($followerId);
        if ($follower->isFollowing($followedId)) {
            $follower->following()->detach($followedId);
            return ['status' => 'info', 'message' => 'Has dejado de seguir a este usuario.'];
        }
        $follower->following()->attach($followedId);
        return ['status' => 'success', 'message' => 'Ahora sigues a este usuario.'];
    }

    /**
     * Alterna el estado de bloqueo entre dos usuarios.
     */
    public function toggleBlock(int $blockerId, int $blockedId): array
    {
        $blocker = User::find($blockerId);
        if ($blocker->hasBlocked($blockedId)) {
            $blocker->blockedUsers()->detach($blockedId);
            return ['status' => 'info', 'message' => 'Usuario desbloqueado.'];
        }
        $blocker->blockedUsers()->attach($blockedId);
        // Al bloquear, también dejamos de seguir preventivamente
        $blocker->following()->detach($blockedId);
        return ['status' => 'warning', 'message' => 'Usuario bloqueado correctamente.'];
    }

    /**
     * Recupera datos estadísticos para los widgets laterales.
     */
    public function getSidebarData(array $blockedIds): array
    {
        return [
            'topGainers' => array_slice($this->marketData->getStockGainers(), 0, 5),
            'topLosers' => array_slice($this->marketData->getStockLosers(), 0, 5),
            'trends' => $this->getMarketTrends(),
            'mostActive' => $this->getMostActiveMarket(),
            'topCreators' => $this->getTopCreators($blockedIds),
            'activeCreators' => $this->getActiveCreators($blockedIds),
        ];
    }

    /**
     * Crea un nuevo post con soporte para imágenes.
     */
    public function storePost(int $userId, array $data, $imageFile = null): Post
    {
        $imagePath = $imageFile ? $imageFile->store('posts', 'public') : null;

        return Post::create([
            'user_id' => $userId,
            'market_asset_id' => $data['market_asset_id'] ?? null,
            'content' => $data['content'],
            'image_path' => $imagePath,
        ]);
    }

    /**
     * Alterna reacciones (Likes/Emojis) en contenido.
     */
    public function toggleLike(int $userId, int $id, string $type, ?string $reaction): array
    {
        $modelClass = $type === 'post' ? Post::class : Comment::class;
        $reaction = $reaction ?? 'like';

        $like = Like::where('user_id', $userId)->where('likeable_id', $id)->where('likeable_type', $modelClass)->first();

        if ($like) {
            if ($like->type === $reaction) {
                $like->delete();
                return ['status' => 'info', 'message' => 'Reacción eliminada.'];
            }
            $like->update(['type' => $reaction]);
            return ['status' => 'success', 'message' => 'Reacción actualizada.'];
        }

        Like::create(['user_id' => $userId, 'likeable_id' => $id, 'likeable_type' => $modelClass, 'type' => $reaction]);
        return ['status' => 'success', 'message' => 'Reacción añadida.'];
    }

    /**
     * Enriquece un post con metadatos reactivos.
     */
    public function enrichPost($post, ?int $userId, ?int $profileOwnerId = null): object
    {
        $post->reactions_summary = $post->likes->groupBy('type')->map->count();
        $myReaction = $post->likes->where('user_id', $userId)->first();
        $post->user_reaction = $myReaction ? $myReaction->type : null;
        $post->is_liked = !!$post->user_reaction;
        $post->is_bookmarked = $userId ? Bookmark::where('user_id', $userId)->where('post_id', $post->id)->exists() : false;
        $post->is_reposted = $userId ? Repost::where('user_id', $userId)->where('post_id', $post->id)->exists() : false;
        $post->is_pinned = $profileOwnerId && $post->id === User::find($profileOwnerId)->pinned_post_id;
        $post->created_at_human = $post->created_at->diffForHumans();
        
        $post->can_edit = $post->user_id === $userId && $post->created_at->diffInMinutes(now()) <= 2;
        $post->can_delete = $post->user_id === $userId;

        $post->comments->each(function($comment) use ($userId) {
            $myReaction = $comment->likes->where('user_id', $userId)->first();
            $comment->user_reaction = $myReaction ? $myReaction->type : null;
            $comment->is_liked = !!$comment->user_reaction;
            $comment->reactions_summary = $comment->likes->groupBy('type')->map->count();
            $comment->created_at_human = $comment->created_at->diffForHumans();
            $comment->replies->each(function($reply) use ($userId) {
                $myReplyId = $reply->likes->where('user_id', $userId)->first();
                $reply->is_liked = !!$myReplyId;
                $reply->created_at_human = $reply->created_at->diffForHumans();
            });
        });

        return $post;
    }

    // --- MÉTODOS PRIVADOS DE ANALÍTICA SOCIAL ---

    private function getMarketTrends()
    {
        return Post::where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('market_asset_id')
            ->select('market_asset_id', DB::raw('count(*) as count'))
            ->groupBy('market_asset_id')->orderBy('count', 'desc')->take(5)->get()
            ->map(function($trend) {
                $asset = MarketAsset::find($trend->market_asset_id);
                return [
                    'name' => $asset->name, 'ticker' => $asset->ticker, 'count' => $trend->count,
                    'price' => $asset->last_price, 'change' => $asset->change_percentage ?? 1.5,
                    'logo' => "https://financialmodelingprep.com/image-stock/{$asset->ticker}.png"
                ];
            });
    }

    private function getMostActiveMarket()
    {
        return collect($this->marketData->getStockActive())->take(8)->map(fn($item) => [
            'name' => $item['name'] ?? $item['symbol'], 'ticker' => $item['symbol'],
            'price' => $item['price'] ?? 0, 'change' => $item['changesPercentage'] ?? 0,
            'volume' => $item['volume'] ?? 0, 'business_volume' => ($item['price'] ?? 0) * ($item['volume'] ?? 0),
            'logo' => "https://financialmodelingprep.com/image-stock/{$item['symbol']}.png"
        ]);
    }

    private function getTopCreators(array $blockedIds)
    {
        return User::select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->join('likes', fn($j) => $j->on('posts.id', '=', 'likes.likeable_id')->where('likes.likeable_type', Post::class))
            ->where('likes.created_at', '>=', now()->subDays(7))
            ->whereNotIn('users.id', $blockedIds)
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->selectRaw('count(likes.id) as reactions_count')
            ->orderBy('reactions_count', 'desc')->take(5)->get();
    }

    private function getActiveCreators(array $blockedIds)
    {
        return User::select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->where('posts.created_at', '>=', now()->subDays(7))
            ->whereNotIn('users.id', $blockedIds)
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->selectRaw('count(posts.id) as posts_count')
            ->orderBy('posts_count', 'desc')->take(5)->get();
    }
}

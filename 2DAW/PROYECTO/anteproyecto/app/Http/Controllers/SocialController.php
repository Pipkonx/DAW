<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\MarketAsset;
use App\Models\User;
use App\Services\MarketDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SocialController extends Controller
{
    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

    public function index(Request $request)
    {
        $filter = $request->query('tab', 'recent');
        $featuredPostId = $request->query('post');
        $featuredPost = null;

        $userId = Auth::id();
        $blockedIds = \DB::table('blocks')
            ->where('blocker_id', $userId)
            ->pluck('blocked_id');

        // Si se solicita un post específico (Deep-linking)
        if ($featuredPostId) {
            $featuredPost = Post::with(['user', 'marketAsset', 'likes', 'comments' => function($q) use ($blockedIds) {
                $q->whereNull('parent_id')
                  ->whereNotIn('user_id', $blockedIds)
                  ->with(['user', 'likes', 'replies' => function($sq) use ($blockedIds) {
                      $sq->whereNotIn('user_id', $blockedIds)
                        ->with(['user', 'likes'])->latest();
                  }])
                  ->latest();
            }])
            ->withCount(['likes', 'comments', 'reposts'])
            ->find($featuredPostId);

            if (!$featuredPost) {
                session()->flash('error', 'La publicación que buscas ya no está disponible o ha sido eliminada.');
            }
        }

        $postsQuery = Post::with(['user', 'marketAsset', 'likes', 'comments' => function($q) use ($blockedIds) {
                $q->whereNull('parent_id')
                  ->whereNotIn('user_id', $blockedIds) // FILTRO COMENTARIOS BLOQUEADOS
                  ->with(['user', 'likes', 'replies' => function($sq) use ($blockedIds) {
                      $sq->whereNotIn('user_id', $blockedIds) // FILTRO RESPUESTAS BLOQUEADAS
                        ->with(['user', 'likes'])->latest();
                  }])
                  ->latest();
            }])
            ->withCount(['likes', 'comments', 'reposts'])
            ->whereNotIn('user_id', $blockedIds); // FILTRO POSTS BLOQUEADOS

        if ($filter === 'following') {
            $followingIds = Auth::user()->following()->pluck('followed_id');
            // Si no sigue a nadie, la lista estará vacía
            $postsQuery->whereIn('user_id', $followingIds);
            $postsQuery->latest();
        } elseif ($filter === 'best') {
            // Lo mejor: Prioridad a interacciones conjuntas (Likes + Comments + Reposts)
            $postsQuery->orderByRaw('(likes_count + comments_count + reposts_count) DESC')->latest();
        } else {
            // General / Tendencias o Reciente
            $postsQuery->latest();
        }

        $userId = Auth::id();
        $posts = $postsQuery->paginate(15)->through(function($post) use ($userId) {
            $post->reactions_summary = $post->likes->groupBy('type')->map->count();
            $myReaction = $post->likes->where('user_id', $userId)->first();
            $post->user_reaction = $myReaction ? $myReaction->type : null;
            $post->is_liked = !!$post->user_reaction;
            
            $post->is_reposted = $post->reposts()->where('user_id', $userId)->exists();
            $post->is_bookmarked = $post->bookmarks()->where('user_id', $userId)->exists();
            $post->is_pinned = $post->user_id === $userId && $post->user->pinned_post_id === $post->id;
            
            $post->can_edit = $post->user_id === $userId && $post->created_at->diffInMinutes(now()) <= 15;
            $post->can_delete = $post->user_id === $userId;
            
            $post->created_at_human = $post->created_at->diffForHumans();
            
            // Enriquecer comentarios
            $post->comments->each(function($comment) use ($userId) {
                $myCommentReaction = $comment->likes->where('user_id', $userId)->first();
                $comment->user_reaction = $myCommentReaction ? $myCommentReaction->type : null;
                $comment->is_liked = !!$comment->user_reaction;
                $comment->reactions_summary = $comment->likes->groupBy('type')->map->count();
                $comment->created_at_human = $comment->created_at->diffForHumans();
                
                $comment->replies->each(function($reply) use ($userId) {
                    $myReplyReaction = $reply->likes->where('user_id', $userId)->first();
                    $reply->user_reaction = $myReplyReaction ? $myReplyReaction->type : null;
                    $reply->is_liked = !!$reply->user_reaction;
                    $reply->created_at_human = $reply->created_at->diffForHumans();
                });
            });
            
            return $post;
        });

        // Sidebar Izquierda: Ganadores y Perdedores
        $topGainers = $this->marketDataService->getStockGainers(); 
        $topLosers = $this->marketDataService->getStockLosers();

        // Sidebar Derecha: Tendencias (Activos más comentados esta semana)
        $trends = Post::where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('market_asset_id')
            ->select('market_asset_id', \DB::raw('count(*) as count'))
            ->groupBy('market_asset_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function($trend) {
                $asset = MarketAsset::find($trend->market_asset_id);
                return [
                    'name' => $asset->name,
                    'ticker' => $asset->ticker,
                    'count' => $trend->count,
                    'price' => $asset->last_price,
                    'change' => $asset->change_percentage ?? 1.5,
                    'logo' => "https://financialmodelingprep.com/image-stock/{$asset->ticker}.png"
                ];
            });

        // Sidebar Derecha: Activos más Negociados (Most Active de la Bolsa)
        $mostActiveRaw = $this->marketDataService->getStockActive();
        $mostActive = collect($mostActiveRaw)->take(8)->map(function($item) {
            $price = $item['price'] ?? 0;
            $volume = $item['volume'] ?? 0;
            return [
                'name' => $item['name'] ?? $item['symbol'],
                'ticker' => $item['symbol'],
                'price' => $price,
                'change' => $item['changesPercentage'] ?? 0,
                'volume' => $volume,
                'business_volume' => ($price * $volume),
                'logo' => "https://financialmodelingprep.com/image-stock/{$item['symbol']}.png"
            ];
        });

        // Sidebar Derecha: Top Creadores (Más populares)
        $topCreators = User::select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->join('likes', function($join) {
                $join->on('posts.id', '=', 'likes.likeable_id')
                    ->where('likes.likeable_type', '=', Post::class);
            })
            ->where('likes.created_at', '>=', now()->subDays(7))
            ->whereNotIn('users.id', $blockedIds) // FILTRO DE BLOQUEO
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->selectRaw('count(likes.id) as reactions_count')
            ->orderBy('reactions_count', 'desc')
            ->take(5)
            ->get();

        // Sidebar Derecha: Top Aportadores (Más actividad semanal)
        $activeCreators = User::select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->where('posts.created_at', '>=', now()->subDays(7))
            ->whereNotIn('users.id', $blockedIds) // FILTRO DE BLOQUEO
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->selectRaw('count(posts.id) as posts_count')
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();

        // Famouse Portfolios
        $famousPortfolios = array_slice([
            ['name' => 'Bill Gates', 'slug' => 'bill-gates', 'avatar' => 'https://financialmodelingprep.com/image-stock/MSFT.png', 'desc' => 'Fundación Gates & Melinda', 'change' => 2.4],
            ['name' => 'Warren Buffett', 'slug' => 'warren-buffett', 'avatar' => 'https://financialmodelingprep.com/image-stock/BRK-B.png', 'desc' => 'Berkshire Hathaway', 'change' => 1.8],
            ['name' => 'Michael Burry', 'slug' => 'michael-burry', 'avatar' => 'https://ui-avatars.com/api/?name=MB', 'desc' => 'Scion Asset Management', 'change' => 5.2],
            ['name' => 'Cathie Wood', 'slug' => 'cathie-wood', 'avatar' => 'https://ui-avatars.com/api/?name=CW', 'desc' => 'ARK Invest Founder', 'change' => -1.2],
        ], 0, 4);

        // Enriquecer el post destacado si existe
        if ($featuredPost) {
            $featuredPost->reactions_summary = $featuredPost->likes->groupBy('type')->map->count();
            $myFeaturedReaction = $featuredPost->likes->where('user_id', $userId)->first();
            $featuredPost->user_reaction = $myFeaturedReaction ? $myFeaturedReaction->type : null;
            $featuredPost->is_liked = !!$featuredPost->user_reaction;
            $featuredPost->is_reposted = $featuredPost->reposts()->where('user_id', $userId)->exists();
            $featuredPost->is_bookmarked = $featuredPost->bookmarks()->where('user_id', $userId)->exists();
            $featuredPost->created_at_human = $featuredPost->created_at->diffForHumans();
            
            $featuredPost->comments->each(function($comment) use ($userId) {
                $comment->created_at_human = $comment->created_at->diffForHumans();
                $comment->replies->each(function($reply) use ($userId) {
                    $reply->created_at_human = $reply->created_at->diffForHumans();
                });
            });
        }

        return Inertia::render('Feed/Index', [
            'posts' => $posts,
            'featuredPost' => $featuredPost,
            'topGainers' => array_slice($topGainers, 0, 5),
            'topLosers' => array_slice($topLosers, 0, 5),
            'trends' => $trends,
            'mostActive' => $mostActive,
            'topCreators' => $topCreators,
            'activeCreators' => $activeCreators,
            'famousPortfolios' => $famousPortfolios,
            'filters' => ['tab' => $filter]
        ]);
    }
    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'market_asset_id' => 'nullable|exists:market_assets,id',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:5120', // Max 5MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'market_asset_id' => $validated['market_asset_id'] ?? null,
            'content' => $validated['content'],
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'Post publicado correctamente.');
    }

    public function storeComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $validated['parent_id'],
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Comentario añadido.');
    }

    public function toggleLike(Request $request)
    {
        $validated = $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string|in:post,comment',
            'type' => 'nullable|string|max:50', // El emoji o 'like'
        ]);

        $typeMap = [
            'post' => Post::class,
            'comment' => Comment::class,
        ];

        $modelClass = $typeMap[$validated['likeable_type']];
        $userId = Auth::id();
        $reactionType = $validated['type'] ?? 'like';

        $like = Like::where('user_id', $userId)
            ->where('likeable_id', $validated['likeable_id'])
            ->where('likeable_type', $modelClass)
            ->first();

        if ($like) {
            if ($like->type === $reactionType) {
                $like->delete();
                return back()->with('info', 'Reacción eliminada.');
            } else {
                $like->update(['type' => $reactionType]);
                return back()->with('success', 'Reacción actualizada.');
            }
        }

        Like::create([
            'user_id' => $userId,
            'likeable_id' => $validated['likeable_id'],
            'likeable_type' => $modelClass,
            'type' => $reactionType,
        ]);

        return back()->with('success', 'Reacción añadida.');
    }

    /**
     * Alternar reposteo (Deprecado - Eliminado por petición del usuario)
     */
    /**
     * Alternar reposteo
     */
    public function toggleRepost(Post $post)
    {
        $userId = Auth::id();
        $repost = \App\Models\Repost::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($repost) {
            $repost->delete();
            return back()->with('info', 'Difusión eliminada.');
        }

        \App\Models\Repost::create([
            'user_id' => $userId,
            'post_id' => $post->id
        ]);

        return back()->with('success', '¡Has difundido este análisis!');
    }

    /**
     * Alternar marcador (Bookmark)
     */
    public function toggleBookmark(Post $post)
    {
        $userId = Auth::id();
        $bookmark = \App\Models\Bookmark::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            return back()->with('info', 'Marcador eliminado.');
        }

        \App\Models\Bookmark::create([
            'user_id' => $userId,
            'post_id' => $post->id
        ]);

        return back()->with('success', 'Publicación guardada en tus marcadores.');
    }

    /**
     * Anclar post al perfil
     */
    public function togglePin(Post $post)
    {
        $user = Auth::user();
        
        if ($post->user_id !== $user->id) {
            abort(403);
        }

        if ($user->pinned_post_id === $post->id) {
            $user->update(['pinned_post_id' => null]);
            return back()->with('info', 'Post desanclado.');
        }

        $user->update(['pinned_post_id' => $post->id]);
        return back()->with('success', 'Post anclado al principio de tu perfil.');
    }

    /**
     * Actualizar post (Ventana de 15 min)
     */
    public function updatePost(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);
        
        if ($post->created_at->diffInMinutes(now()) > 15) {
            return back()->with('error', 'El tiempo límite de 15 minutos para editar ha expirado.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->update(['content' => $validated['content']]);

        return back()->with('success', 'Post actualizado.');
    }

    /**
     * Eliminar post (Ventana de 15 min)
     */
    public function deletePost(Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return back()->with('success', 'Post eliminado.');
    }

    /**
     * Reportar contenido inapropiado (Post o Comentario).
     */
    public function reportContent(Request $request)
    {
        $validated = $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string|in:post,comment',
            'reason' => 'required|string|max:500',
        ]);

        $typeMap = [
            'post' => Post::class,
            'comment' => Comment::class,
        ];

        \App\Models\Report::create([
            'user_id' => Auth::id(),
            'reportable_id' => $validated['reportable_id'],
            'reportable_type' => $typeMap[$validated['reportable_type']],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Reporte enviado. El equipo de moderación lo revisará.');
    }
}

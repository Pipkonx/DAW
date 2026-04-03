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
        $filter = $request->query('tab', 'recent'); // recent, following, best
        
        $postsQuery = Post::with(['user', 'marketAsset', 'likes', 'comments' => function($q) {
                $q->whereNull('parent_id')
                  ->with(['user', 'likes', 'replies' => function($sq) {
                      $sq->with(['user', 'likes'])->latest();
                  }])
                  ->latest();
            }])
            ->withCount(['likes', 'comments', 'reposts']);

        if ($filter === 'best') {
            $postsQuery->orderBy('likes_count', 'desc');
        } else {
            $postsQuery->latest();
        }

        $userId = Auth::id();
        $posts = $postsQuery->paginate(15)->through(function($post) use ($userId) {
            $post->reactions_summary = $post->likes->groupBy('type')->map->count();
            $myReaction = $post->likes->where('user_id', $userId)->first();
            $post->user_reaction = $myReaction ? $myReaction->type : null;
            $post->is_liked = !!$post->user_reaction;
            
            $post->is_reposted = false; // Deprecated
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
                    'change' => 1.5, // Mock or fetch real
                    'logo' => "https://financialmodelingprep.com/image-stock/{$asset->ticker}.png"
                ];
            });

        // Sidebar Derecha: Top Creadores (Más likes recibidos esta semana)
        $topCreators = User::select('users.id', 'users.name', 'users.avatar')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->join('likes', function($join) {
                $join->on('posts.id', '=', 'likes.likeable_id')
                    ->where('likes.likeable_type', '=', Post::class);
            })
            ->where('likes.created_at', '>=', now()->subDays(7))
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->selectRaw('count(likes.id) as reactions_count')
            ->orderBy('reactions_count', 'desc')
            ->take(5)
            ->get();

        // Famouse Portfolios (Static data as requested)
        $famousPortfolios = [
            ['name' => 'Bill Gates', 'slug' => 'bill-gates', 'avatar' => 'https://financialmodelingprep.com/image-stock/MSFT.png', 'desc' => 'Fundación Gates & Melinda', 'change' => 2.4],
            ['name' => 'Warren Buffett', 'slug' => 'warren-buffett', 'avatar' => 'https://financialmodelingprep.com/image-stock/BRK-B.png', 'desc' => 'Berkshire Hathaway', 'change' => 1.8],
            ['name' => 'Michael Burry', 'slug' => 'michael-burry', 'avatar' => 'https://ui-avatars.com/api/?name=MB', 'desc' => 'Scion Asset Management', 'change' => 5.2],
            ['name' => 'Cathie Wood', 'slug' => 'cathie-wood', 'avatar' => 'https://ui-avatars.com/api/?name=CW', 'desc' => 'ARK Invest Founder', 'change' => -1.2],
            ['name' => 'Bill Ackman', 'slug' => 'bill-ackman', 'avatar' => 'https://ui-avatars.com/api/?name=BA', 'desc' => 'Pershing Square CEO', 'change' => 0.4],
            ['name' => 'Ray Dalio', 'slug' => 'ray-dalio', 'avatar' => 'https://ui-avatars.com/api/?name=RD', 'desc' => 'Bridgewater Associates', 'change' => 1.2],
            ['name' => 'Ken Griffin', 'slug' => 'ken-griffin', 'avatar' => 'https://ui-avatars.com/api/?name=KG', 'desc' => 'Citadel Founder', 'change' => 2.1],
            ['name' => 'Jim Simons', 'slug' => 'jim-simons', 'avatar' => 'https://ui-avatars.com/api/?name=JS', 'desc' => 'Renaissance Tech', 'change' => 4.5],
        ];

        return Inertia::render('Feed/Index', [
            'posts' => $posts,
            'topGainers' => array_slice($topGainers, 0, 5),
            'topLosers' => array_slice($topLosers, 0, 5),
            'trends' => $trends,
            'topCreators' => $topCreators,
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
    public function toggleRepost(Post $post)
    {
        return back()->with('error', 'La función de difusión ha sido desactivada.');
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
            'user_id' => Auth::id(),
            'reportable_id' => $validated['reportable_id'],
            'reportable_type' => $typeMap[$validated['reportable_type']],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Reporte enviado. El equipo de moderación lo revisará.');
    }
}

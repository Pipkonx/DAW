<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Services\Social\SocialMediaService;
use App\Services\Storage\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * SocialController - Orquestador del Muro Comunitario.
 * 
 * Gestiona el muro de noticias global y las interacciones entre usuarios.
 * Delega la lógica de negocio pesada en SocialMediaService.
 */
class SocialController extends Controller
{
    protected $socialService;
    protected $storageService;

    public function __construct(SocialMediaService $socialService, FileStorageService $storageService)
    {
        $this->socialService = $socialService;
        $this->storageService = $storageService;
    }

    /**
     * Muestra el Feed de noticias con filtros y widgets laterales.
     */
    public function index(Request $request)
    {
        $filter = $request->query('tab', 'recent');
        $featuredPostId = $request->query('post');
        $userId = Auth::id();

        // Obtener IDs de usuarios bloqueados para filtrar contenido
        $blockedIds = \DB::table('blocks')->where('blocker_id', $userId)->pluck('blocked_id')->toArray();

        // Registro específico (Deep-linking)
        $featuredPost = null;
        if ($featuredPostId) {
            $post = Post::with(['user', 'marketAsset', 'likes', 'comments.user'])->withCount(['likes', 'comments', 'reposts'])->find($featuredPostId);
            if ($post) $featuredPost = $this->socialService->enrichPost($post, $userId);
        }

        // Composición del Feed y Widgets mediante el Servicio Social
        $posts = $this->socialService->getFeedData($filter, $userId, $blockedIds);
        $sidebar = $this->socialService->getSidebarData($blockedIds);

        return Inertia::render('Feed/Index', array_merge($sidebar, [
            'posts' => $posts,
            'featuredPost' => $featuredPost,
            'filters' => ['tab' => $filter],
            'famousPortfolios' => $this->getFamousPortfolios()
        ]));
    }

    /**
     * Publicar un nuevo análisis en el muro.
     */
    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'market_asset_id' => 'nullable|exists:market_assets,id',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:5120',
        ]);

        $this->socialService->storePost(Auth::id(), $validated, $request->file('image'));
        return back()->with('success', 'Publicación compartida correctamente.');
    }

    /**
     * Añadir un comentario a una publicación.
     */
    public function storeComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Comentario añadido.');
    }

    /**
     * Gestionar reacciones (Likes / emojis).
     */
    public function toggleLike(Request $request)
    {
        $validated = $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string|in:post,comment',
            'type' => 'nullable|string|max:50',
        ]);

        $result = $this->socialService->toggleLike(Auth::id(), $validated['likeable_id'], $validated['likeable_type'], $validated['type']);
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Difundir un post en el muro propio (Repost).
     */
    public function toggleRepost(Post $post)
    {
        $userId = Auth::id();
        $repost = \App\Models\Repost::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($repost) {
            $repost->delete();
            return back()->with('info', 'Difusión eliminada.');
        }

        \App\Models\Repost::create(['user_id' => $userId, 'post_id' => $post->id]);
        return back()->with('success', '¡Has difundido este análisis!');
    }

    /**
     * Guardar publicación en marcadores personales.
     */
    public function toggleBookmark(Post $post)
    {
        $userId = Auth::id();
        $bookmark = \App\Models\Bookmark::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            return back()->with('info', 'Marcador eliminado.');
        }

        \App\Models\Bookmark::create(['user_id' => $userId, 'post_id' => $post->id]);
        return back()->with('success', 'Publicación guardada.');
    }

    /**
     * Anclar publicación al perfil de usuario.
     */
    public function togglePin(Post $post)
    {
        $user = Auth::user();
        if ($post->user_id !== $user->id) abort(403);

        $isPinned = $user->pinned_post_id === $post->id;
        $user->update(['pinned_post_id' => $isPinned ? null : $post->id]);

        return back()->with('info', $isPinned ? 'Post desanclado.' : 'Post anclado al principio.');
    }

    /**
     * Editar contenido de una publicación (Ventana de 15 min).
     */
    public function updatePost(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);
        if ($post->created_at->diffInMinutes(now()) > 2) return back()->with('error', 'El tiempo de edición (2 min) ha expirado.');

        $validated = $request->validate(['content' => 'required|string|max:1000']);
        $post->update(['content' => $validated['content']]);

        return back()->with('success', 'Publicación actualizada.');
    }

    /**
     * Eliminar una publicación.
     */
    public function deletePost(Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);
        
        $this->storageService->deleteFile($post->image_path);
        $post->delete();

        return back()->with('success', 'Publicación eliminada.');
    }

    /**
     * Reportar contenido inapropiado.
     */
    public function reportContent(Request $request)
    {
        $validated = $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string|in:post,comment',
            'reason' => 'required|string|max:500',
        ]);

        $typeMap = ['post' => Post::class, 'comment' => Comment::class];

        \App\Models\Report::create([
            'user_id' => Auth::id(),
            'reportable_id' => $validated['reportable_id'],
            'reportable_type' => $typeMap[$validated['reportable_type']],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Reporte enviado para moderación.');
    }

    /**
     * Retorna listado de carteras famosas (Mock/Datos externos).
     */
    private function getFamousPortfolios(): array
    {
        return [
            ['name' => 'Bill Gates', 'slug' => 'bill-gates', 'avatar' => 'https://financialmodelingprep.com/image-stock/MSFT.png', 'desc' => 'Gates & Melinda Foundation', 'change' => 2.4],
            ['name' => 'Warren Buffett', 'slug' => 'warren-buffett', 'avatar' => 'https://financialmodelingprep.com/image-stock/BRK-B.png', 'desc' => 'Berkshire Hathaway', 'change' => 1.8],
            ['name' => 'Michael Burry', 'slug' => 'michael-burry', 'avatar' => 'https://ui-avatars.com/api/?name=MB', 'desc' => 'Scion Asset Management', 'change' => 5.2],
        ];
    }
}

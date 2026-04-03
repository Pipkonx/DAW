<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\User;
use App\Models\Post;

use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    /**
     * Mostrar el muro de un usuario específico.
     */
    public function show($username = null)
    {
        // Si no se proporciona username, mostrar el perfil del usuario autenticado
        if (!$username && Auth::check()) {
            return redirect()->route('social.profile', ['username' => Auth::user()->username]);
        }

        // Compatibilidad: Si es un ID numérico, buscar y redirigir al username
        if (is_numeric($username)) {
            $user = User::findOrFail($username);
            return redirect()->route('social.profile', ['username' => $user->username]);
        }

        // Búsqueda por username (Identificador único)
        $user = User::where('username', $username)->firstOrFail();

        $userId = Auth::id();

        // Cargar contadores de seguidores
        $user->loadCount(['followers', 'following']);
        $isFollowing = $userId ? $user->followers()->where('follower_id', $userId)->exists() : false;

        // Obtener posts del usuario + reposts
        $posts = Post::where('user_id', $user->id)
            ->orWhereHas('reposts', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user', 'marketAsset', 'likes', 'bookmarks', 'reposts', 'comments.user', 'comments.likes', 'comments.replies.user', 'comments.replies.likes'])
            ->withCount(['likes', 'comments', 'reposts'])
            ->latest()
            ->get()
            ->map(function($post) use ($userId, $user) {
                $post->reactions_summary = $post->likes->groupBy('type')->map->count();
                $myReaction = $post->likes->where('user_id', $userId)->first();
                $post->user_reaction = $myReaction ? $myReaction->type : null;
                $post->is_liked = !!$post->user_reaction;
                
                $post->is_bookmarked = $post->bookmarks->where('user_id', $userId)->isNotEmpty();
                $post->is_reposted = $post->reposts->where('user_id', $userId)->isNotEmpty();
                $post->is_pinned = $user->pinned_post_id === $post->id;
                
                // Lógica de permisos separada
                $post->can_edit = $post->user_id === $userId && $post->created_at->diffInMinutes(now()) <= 15;
                $post->can_delete = $post->user_id === $userId; // Borrado ilimitado para el dueño
                
                $post->created_at_human = $post->created_at->diffForHumans();
                
                // Marcar si el post es un reshare en este muro
                $post->wall_is_repost = $post->user_id !== $user->id;
                
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

        // Ordenar: Pinned post primero
        if ($user->pinned_post_id) {
            $posts = $posts->sortByDesc(function($post) use ($user) {
                return $post->id === $user->pinned_post_id ? 1 : 0;
            })->values();
        }

        // Obtener Marcadores (Solo si es su propio perfil)
        $bookmarks = [];
        if ($userId === $user->id) {
            $bookmarks = Post::whereIn('id', function($query) use ($userId) {
                $query->select('post_id')->from('bookmarks')->where('user_id', $userId);
            })
            ->with(['user', 'marketAsset', 'likes', 'bookmarks', 'reposts', 'comments.user', 'comments.likes', 'comments.replies.user', 'comments.replies.likes'])
            ->withCount(['likes', 'comments', 'reposts'])
            ->latest()
            ->get()
            ->map(function($post) use ($userId) {
                $post->reactions_summary = $post->likes->groupBy('type')->map->count();
                $myReaction = $post->likes->where('user_id', $userId)->first();
                $post->user_reaction = $myReaction ? $myReaction->type : null;
                $post->is_liked = !!$post->user_reaction;
                $post->is_bookmarked = true;
                $post->is_reposted = $post->reposts->where('user_id', $userId)->isNotEmpty();
                $post->created_at_human = $post->created_at->diffForHumans();
                
                // Enriquecer comentarios en marcadores
                $post->comments->each(function($comment) use ($userId) {
                    $comment->created_at_human = $comment->created_at->diffForHumans();
                });

                return $post;
            });
        }

        // Formatear fecha de unión
        $user->joined_at = $user->created_at->translatedFormat('F \d\e Y');

        // Estado de Bloqueo
        $isBlocked = $userId ? \DB::table('blocks')->where('blocker_id', $userId)->where('blocked_id', $user->id)->exists() : false;

        return Inertia::render('Social/Profile', [
            'profileUser' => $user,
            'posts' => $posts,
            'bookmarks' => $bookmarks,
            'isOwnProfile' => $userId === $user->id,
            'isFollowing' => $isFollowing,
            'isBlocked' => $isBlocked
        ]);
    }

    /**
     * Actualizar información del perfil (Nombre, Bio, Username, Banner).
     */
    public function updateSocial(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'banner' => 'nullable|image|max:5120', // 5MB
        ]);

        if ($request->hasFile('banner')) {
            // Eliminar banner antiguo si existe
            if ($user->banner_path) {
                Storage::disk('public')->delete($user->banner_path);
            }
            $path = $request->file('banner')->store('banners', 'public');
            $validated['banner_path'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado visualmente.');
    }

    /**
     * Seguir o dejar de seguir a un usuario.
     */
    public function toggleFollow(User $user)
    {
        $currentUserId = Auth::id();

        if ($currentUserId === $user->id) {
            return back()->with('error', 'No puedes seguirte a ti mismo.');
        }

        $isFollowing = \DB::table('followers')
            ->where('follower_id', $currentUserId)
            ->where('followed_id', $user->id)
            ->exists();

        if ($isFollowing) {
            // Dejar de seguir
            \DB::table('followers')
                ->where('follower_id', $currentUserId)
                ->where('followed_id', $user->id)
                ->delete();
            return back()->with('info', 'Has dejado de seguir a ' . $user->name);
        } else {
            // Seguir
            \DB::table('followers')->insert([
                'follower_id' => $currentUserId,
                'followed_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return back()->with('success', 'Ahora sigues a ' . $user->name);
        }
    }

    /**
     * Block or unblock a user.
     */
    public function block(User $user): RedirectResponse
    {
        $currentUserId = Auth::id();

        if ($user->id === $currentUserId) {
            return back()->with('error', 'No puedes bloquearte a ti mismo.');
        }

        $isBlocked = \DB::table('blocks')
            ->where('blocker_id', $currentUserId)
            ->where('blocked_id', $user->id)
            ->exists();

        if ($isBlocked) {
            // Desbloquear
            \DB::table('blocks')
                ->where('blocker_id', $currentUserId)
                ->where('blocked_id', $user->id)
                ->delete();
            
            return back()->with('info', 'Has desbloqueado a ' . $user->name);
        } else {
            // Bloquear
            \DB::table('blocks')->insert([
                'blocker_id' => $currentUserId,
                'blocked_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Al bloquear, también dejamos de seguir mutuamente (limpieza)
            \DB::table('followers')
                ->where(function($q) use ($currentUserId, $user) {
                    $q->where('follower_id', $currentUserId)->where('followed_id', $user->id);
                })
                ->orWhere(function($q) use ($currentUserId, $user) {
                    $q->where('follower_id', $user->id)->where('followed_id', $currentUserId);
                })
                ->delete();

            return back()->with('success', 'Has bloqueado a ' . $user->name . '. Ya no verás su contenido.');
        }
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $blockedUsers = User::whereIn('id', function($query) use ($user) {
            $query->select('blocked_id')->from('blocks')->where('blocker_id', $user->id);
        })->get(['id', 'name', 'username', 'avatar']);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'blockedUsers' => $blockedUsers,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Manejo de Avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                // Si es una URL externa (Google) no borrar, si es local sí
                if (!filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = "/storage/{$path}";
        } elseif ($request->boolean('delete_photo')) {
            // Borrado solicitado de la foto actual
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $validated['avatar'] = null;
        }

        // Manejo de Banner
        if ($request->hasFile('banner')) {
            if ($user->banner_path) {
                Storage::disk('public')->delete($user->banner_path);
            }
            $path = $request->file('banner')->store('banners', 'public');
            $validated['banner_path'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Información de perfil actualizada.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

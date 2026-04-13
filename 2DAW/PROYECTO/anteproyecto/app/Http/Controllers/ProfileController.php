<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Post;
use App\Services\Social\SocialMediaService;
use App\Services\Storage\FileStorageService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

/**
 * ProfileController - Centro de mando de la identidad del usuario.
 * 
 * Orquestador de perfiles, muros sociales y gestión de cuentas.
 * Delega la lógica de negocio en servicios de alta cohesión.
 */
class ProfileController extends Controller
{
    protected $socialService;
    protected $storageService;
    protected $userService;

    public function __construct(
        SocialMediaService $socialService,
        FileStorageService $storageService,
        UserService $userService
    ) {
        $this->socialService = $socialService;
        $this->storageService = $storageService;
        $this->userService = $userService;
    }

    /**
     * Muestra el muro social de un usuario (Perfil Público).
     */
    public function show($username = null)
    {
        // Redirección si no se especifica usuario (ver mi propio perfil)
        if (!$username && Auth::check()) {
            $fallback = Auth::user()->username ?? ('user_' . Auth::id());
            return redirect()->route('social.profile', ['username' => $fallback]);
        }

        // Búsqueda del usuario: Primero por username exacto, luego por ID si sigue el patrón user_{id}
        $user = User::where('username', $username)->first();

        if (!$user && preg_match('/^user_(\d+)$/', $username, $matches)) {
            $user = User::where(function($q) {
                $q->whereNull('username')->orWhere('username', '');
            })->find($matches[1]);
        }

        if (!$user) abort(404);

        $authUserId = Auth::id();

        // Enriquecimiento de relación social
        $user->loadCount(['followers', 'following']);
        $isFollowing = $authUserId ? $user->followers()->where('follower_id', $authUserId)->exists() : false;
        $isBlocked = $authUserId ? \DB::table('blocks')->where('blocker_id', $authUserId)->where('blocked_id', $user->id)->exists() : false;

        // Composición del Muro Delegada al Servicio Social
        $blockedIds = $authUserId ? \DB::table('blocks')->where('blocker_id', $authUserId)->pluck('blocked_id')->toArray() : [];
        $posts = $this->socialService->getWallData($user->id, $authUserId, $blockedIds);

        // Marcadores (Solo si es su propio perfil)
        $bookmarks = [];
        if ($authUserId === $user->id) {
            $bookmarksData = Post::whereIn('id', function($q) use ($authUserId) {
                $q->select('post_id')->from('bookmarks')->where('user_id', $authUserId);
            })->with(['user', 'marketAsset', 'likes', 'bookmarks', 'reposts', 'comments.user'])
              ->withCount(['likes', 'comments', 'reposts'])->latest()->get();

            $bookmarks = $bookmarksData->map(fn($p) => $this->socialService->enrichPost($p, $authUserId));
        }

        return Inertia::render('Social/Profile', [
            'profileUser' => $user,
            'posts' => $posts,
            'bookmarks' => $bookmarks,
            'isOwnProfile' => $authUserId === $user->id,
            'isFollowing' => $isFollowing,
            'isBlocked' => $isBlocked,
            'joined_at' => $user->created_at->translatedFormat('F \d\e Y')
        ]);
    }

    /**
     * Edición específica de Bio, Nombre y Banner Red Social.
     */
    public function updateSocial(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'banner' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('banner')) {
            $this->storageService->deleteFile($user->banner_path);
            $validated['banner_path'] = $this->storageService->storeBanner($request->file('banner'));
        }

        $user->update($validated);
        return back()->with('success', 'Perfil visual actualizado correctamente.');
    }

    /**
     * Alternar seguimiento (Follow/Unfollow).
     */
    public function toggleFollow(User $user)
    {
        $result = $this->socialService->toggleFollow(Auth::id(), $user->id);
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Alternar estado de bloqueo entre usuarios.
     */
    public function block(User $user): RedirectResponse
    {
        $result = $this->socialService->toggleBlock(Auth::id(), $user->id);
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Muestra el formulario de edición de ajustes de cuenta.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $sub = $user->subscriptions()->first();
        $subscriptionData = null;

        if ($sub && $sub->active()) {
            $daysLeft = $sub->ends_at ? (int) now()->diffInDays($sub->ends_at, false) : null;
            $subscriptionData = [
                'tier'       => $user->tier,
                'status'     => $user->subscription_status,
                'ends_at'    => $sub->ends_at?->format('d/m/Y'),
                'days_left'  => $daysLeft !== null ? max(0, $daysLeft) : null,
                'on_grace'   => $sub->onGracePeriod(),
            ];
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            'status'          => session('status'),
            'blockedUsers'    => $this->userService->getBlockedUsers($request->user()),
            'subscription'    => $subscriptionData,
        ]);
    }

    /**
     * Actualizar información de cuenta (Core, Avatar).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Gestión Modularizada de Avatar
        if ($request->hasFile('avatar')) {
            if (!$this->storageService->isExternal($user->avatar)) {
                $this->storageService->deleteFile($user->avatar);
            }
            $validated['avatar'] = $this->storageService->storeAvatar($request->file('avatar'));
        } elseif ($request->boolean('delete_photo')) {
            $this->storageService->deleteFile($user->avatar);
            $validated['avatar'] = null;
        }

        // Gestión Modularizada de Banner
        if ($request->hasFile('banner')) {
            $this->storageService->deleteFile($user->banner_path);
            $validated['banner_path'] = $this->storageService->storeBanner($request->file('banner'));
        }

        $this->userService->updateProfile($user, $validated);

        return Redirect::route('profile.edit')->with('success', 'Ajustes de cuenta actualizados.');
    }

    /**
     * Eliminación definitiva de la cuenta.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);
        
        $this->userService->deleteAccount($request->user());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Extender la membresía del usuario añadiendo más días.
     */
    public function renewSubscription(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $user = Auth::user();
        $currentTier = $user->tier !== 'none' ? $user->tier : 'basic';
        $this->userService->updateSubscription($user, $currentTier, $validated['days']);

        return back()->with('success', "Membresía extendida {$validated['days']} días más correctamente.");
    }

    /**
     * Cancelar la suscripción activa del usuario de forma inmediata.
     */
    public function cancelSubscription(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $sub = \DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->where('type', 'default')
            ->first();

        if (!$sub || $sub->stripe_status !== 'active') {
            return back()->with('error', 'No tienes ninguna suscripción activa que cancelar.');
        }

        \DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->where('type', 'default')
            ->update([
                'stripe_status' => 'canceled',
                'ends_at'       => now(),
                'updated_at'    => now(),
            ]);

        return back()->with('success', 'Suscripción cancelada. Puedes renovarla en cualquier momento.');
    }
}

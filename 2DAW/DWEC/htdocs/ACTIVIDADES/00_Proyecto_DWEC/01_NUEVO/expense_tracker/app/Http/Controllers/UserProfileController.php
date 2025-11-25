<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Muestra el formulario para cambiar la contraseña del usuario.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Cambia la contraseña del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        // Valida los campos de la solicitud
        $request->validate([
            'current_password' => 'required', // La contraseña actual es obligatoria
            'new_password' => 'required|string|min:8|confirmed', // La nueva contraseña es obligatoria, debe ser una cadena, mínimo 8 caracteres y confirmada
        ]);

        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Verifica si la contraseña actual proporcionada coincide con la contraseña del usuario
        if (!Hash::check($request->current_password, $user->password)) {
            // Si no coincide, redirige de vuelta con un error
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        // Hashea y guarda la nueva contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Redirige al panel con un mensaje de éxito
        return redirect()->route('panel')->with('success', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Muestra el formulario para editar el nombre del usuario.
     *
     * @return \Illuminate\View\View
     */
    public function showEditNameForm()
    {
        return view('auth.edit-name');
    }

    /**
     * Actualiza el nombre del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateName(Request $request)
    {
        // Valida el campo del nombre
        $request->validate([
            'name' => 'required|string|max:255', // El nombre es obligatorio, debe ser una cadena y máximo 255 caracteres
        ]);

        // Obtiene el usuario autenticado
        $user = Auth::user();
        // Actualiza el nombre del usuario
        $user->name = $request->name;
        $user->save();

        // Redirige al panel con un mensaje de éxito
        return redirect()->route('panel')->with('success', 'Nombre de usuario actualizado exitosamente.');
    }
}

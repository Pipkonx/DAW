<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaccion;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Database;

class ControladorTransaccion extends Controller
{
    /**
     * Obtiene las subcategorías de una categoría principal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubcategorias(Request $request)
    {
        $parentId = $request->input('parent_id');
        $subcategorias = Auth::user()->categorias()->where('parent_id', $parentId)->get(['id', 'nombre']);
        return response()->json($subcategorias);
    }

    /**
     * Muestra una lista de transacciones del usuario autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $transacciones = Auth::user()->transacciones()->with('categoria')->latest()->get();
        return view('transacciones.indice', compact('transacciones'));
    }

    /**
     * Muestra el formulario para crear una nueva transacción.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Obtener las categorías del usuario autenticado
                $categorias = Auth::user()->categorias()->whereNull('parent_id')->get();
        return view('transacciones.crear', compact('categorias'));
    }

    /**
     * Almacena una nueva transacción en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'required|date',
            'tipo' => 'required|in:ingreso,gasto,inversion',
            'categoria_principal_id' => 'required|exists:categorias,id',
            'categori-id' => 'nullable|exists:categorias,id',
        ]);

        $userId = Auth::id();

        if (is_null($userId)) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para registrar transacciones.');
        }

        // Verificar si el usuario realmente existe en la base de datos
        $userExists = User::where('id', $userId)->exists();

        if (!$userExists) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado o tu cuenta no es válida. Por favor, inicia sesión de nuevo.');
        }
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        try {
            $pdo->beginTransaction();
            // Determinar el ID de la categoría: si hay subcategoría, usarla; de lo contrario, usar la categoría principal.
            $categoriaId = $request->input('categori-id') ?? $request->input('categoria_principal_id');

            // Insertar transacción
            $stmt = $pdo->prepare("INSERT INTO transacciones (user_id, category_id, monto, tipo, fecha, descripcion, created_at, updated_at) VALUES (:user_id, :category_id, :monto, :tipo, :fecha, :descripcion, NOW(), NOW())");
            $stmt->execute([
                'user_id' => $userId,
                'category_id' => $categoriaId,
                'monto' => $request->monto,
                'tipo' => $request->tipo,
                'fecha' => $request->fecha,
                'descripcion' => $request->descripcion,
            ]);

            $pdo->commit();

            return redirect()->route('home')->with('success', 'Transacción creada exitosamente.');

        } catch (PDOException $e) {
            $pdo->rollBack();
            return redirect()->back()->with('error', 'Error al crear la transacción: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de una transacción específica.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\View\View
     */
    public function show(Transaccion $transaccion)
    {
        // Asegurarse de que el usuario autenticado es el propietario de la transacción
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }
        return view('transacciones.mostrar', compact('transaccion'));
    }

    /**
     * Muestra el formulario para editar una transacción existente.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\View\View
     */
    public function edit(Transaccion $transaccion)
    {
        // Asegurarse de que el usuario autenticado es el propietario de la transacción
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }
        $categorias = Auth::user()->categorias()->get();
        return view('transacciones.editar', compact('transaccion', 'categorias'));
    }

    /**
     * Actualiza una transacción existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Transaccion $transaccion)
    {
        // Asegurarse de que el usuario autenticado es el propietario de la transacción
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        $request->validate([
            'categori-id' => 'required|exists:categorias,id',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => 'required|in:ingreso,gasto',
        ]);

        $transaccion->update($request->all());

        return redirect()->route('transacciones.index')->with('success', 'Transacción actualizada exitosamente.');
    }

    /**
     * Elimina una transacción de la base de datos.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Transaccion $transaccion)
    {
        // Asegurarse de que el usuario autenticado es el propietario de la transacción
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        $transaccion->delete();

        return redirect()->route('transacciones.index')->with('success', 'Transacción eliminada exitosamente.');
    }
}

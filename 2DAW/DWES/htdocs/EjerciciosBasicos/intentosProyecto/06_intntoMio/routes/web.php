<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', function () {
    $ok = AuthController::login(request('username'), request('password'), request()->has('remember'));
    return $ok ? redirect('/tasks') : redirect('/login');
});

Route::get('/logout', function () {
    AuthController::logout();
    return redirect('/login');
});

Route::get('/tasks', function () {
    if (!AuthController::check()) return redirect('/login');
    $tareas = TaskController::index();
    return view('tareas.listado', compact('tareas'));
});

Route::get('/tasks/create', function () {
    if (!AuthController::check()) return redirect('/login');
    return view('tareas.crear');
});

Route::post('/tasks/create', function () {
    if (!AuthController::check()) return redirect('/login');
    $datos = request()->all();
    if (request()->hasFile('archivo')) {
        $archivo = request()->file('archivo');
        $nombre = $archivo->getClientOriginalName();
        $destino = storage_path('app/public/uploads');
        if (!is_dir($destino)) { mkdir($destino, 0777, true); }
        $archivo->move($destino, $nombre);
        $datos['archivo'] = $nombre;
    }
    TaskController::create($datos);
    return redirect('/tasks');
});

Route::get('/tasks/view', function () {
    if (!AuthController::check()) return redirect('/login');
    $id = request('id', 0);
    $tarea = TaskController::show($id);
    return view('tareas.ver', compact('tarea'));
});

Route::get('/tasks/edit', function () {
    if (!AuthController::check()) return redirect('/login');
    $id = request('id', 0);
    $tarea = TaskController::show($id);
    return view('tareas.editar', compact('tarea'));
});

Route::post('/tasks/edit', function () {
    if (!AuthController::check()) return redirect('/login');
    $id = request('id');
    TaskController::update($id, request()->all());
    return redirect('/tasks');
});

Route::get('/tasks/delete', function () {
    if (!AuthController::check()) return redirect('/login');
    $id = request('id', 0);
    TaskController::delete($id);
    return redirect('/tasks');
});

Route::get('/', function () {
    return AuthController::check() ? redirect('/tasks') : redirect('/login');
});

Route::get('/debug/users', function () {
    $usuarios = UserController::all();
    return response()->make("Usuarios en BD (tareasdb.users):\n".collect($usuarios)->map(function ($u) {
        return "- username: ".$u['username']." | role: ".($u['role'] ?? '');
    })->implode("\n"), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
});

Route::get('/debug/seed-admin', function () {
    $usuarios = UserController::all();
    if (!$usuarios || count($usuarios) === 0) {
        UserController::create(['username' => 'admin', 'password' => 'admin', 'role' => 'admin']);
        return response('Creado usuario admin/admin', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
    return response('Ya existen usuarios; no se crea admin por seguridad', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
});

Route::get('/debug/seed-usuarios', function () {
    $admin = User::findByUsername('admin');
    if (!$admin) {
        User::create(['username' => 'admin', 'password' => 'admin123', 'role' => 'admin']);
    }
    $operario = User::findByUsername('operario');
    if (!$operario) {
        User::create(['username' => 'operario', 'password' => 'operario123', 'role' => 'operario']);
    }
    return response('Usuarios de desarrollo preparados: admin/admin123 y operario/operario123', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
});

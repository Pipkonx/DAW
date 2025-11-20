<?php
namespace App\Http\Controllers;
use App\Models\Task;

class TaskController {
    public static function index() {
        return Task::all();
    }

    public static function show($id) {
        return Task::find($id);
    }

    public static function create($datos) {
        return Task::create($datos);
    }

    public static function update($id, $datos) {
        Task::update($id, $datos);
    }

    public static function delete($id) {
        Task::delete($id);
    }
}

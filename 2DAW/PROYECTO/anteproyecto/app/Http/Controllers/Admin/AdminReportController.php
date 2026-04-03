<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminReportController extends Controller
{
    /**
     * Listar todos los reportes pendientes.
     */
    public function index()
    {
        $reports = Report::with(['user', 'reportable.user'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Reports', [
            'reports' => $reports
        ]);
    }

    /**
     * Descartar un reporte (borrar el reporte pero mantener el contenido).
     */
    public function dismiss(Report $report)
    {
        $report->delete();
        return back()->with('success', 'Reporte descartado correctamente.');
    }

    /**
     * Ejecutar acción: Borrar el contenido reportado y el reporte.
     */
    public function destroyContent(Report $report)
    {
        $content = $report->reportable;

        if ($content) {
            // Si es un post y tiene imagen, borrarla
            if ($content instanceof Post && $content->image_path) {
                Storage::disk('public')->delete($content->image_path);
            }
            
            $content->delete();
        }

        $report->delete();

        return back()->with('success', 'Contenido eliminado y reporte cerrado.');
    }
}

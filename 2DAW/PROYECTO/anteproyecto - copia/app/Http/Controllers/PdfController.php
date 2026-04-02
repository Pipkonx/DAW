<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function download()
    {
        $data = [
            'title' => 'Anteproyecto Fin de Grado',
            'date' => date('d/m/Y'),
            'author' => 'Rafael', // Asumo el nombre del usuario por el path
        ];

        $pdf = Pdf::loadView('pdf.anteproyecto', $data);
        
        // Configuración opcional
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Anteproyecto-2.pdf');
    }

    public function stream()
    {
        $data = [
            'title' => 'Anteproyecto Fin de Grado',
            'date' => date('d/m/Y'),
            'author' => 'Rafael',
        ];

        $pdf = Pdf::loadView('pdf.anteproyecto', $data);
        return $pdf->stream('Anteproyecto-2.pdf');
    }
}

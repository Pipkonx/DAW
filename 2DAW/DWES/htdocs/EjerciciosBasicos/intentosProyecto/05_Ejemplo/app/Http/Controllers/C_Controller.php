<?php

namespace App\Http\Controllers;

/**
 * Clase base de controladores de la aplicación.
 */
abstract class C_Controller
{
    const TAREAS_POR_PAGINA = 10;

    /**
     * Helper method to get pagination data.
     *
     * @param \App\Models\Tareas $modelo The Tareas model instance.
     * @param string|null $countMethod The name of the method to use for counting total elements (e.g., 'contarPorOperario').
     * @param mixed $countParam Parameter for the count method (e.g., operarioEncargado).
     * @return array Contains 'paginaActual', 'totalElementos', 'totalPaginas'.
     */
    protected function getPaginationData(\App\Models\M_Tareas $modelo, ?string $countMethod = null, $countParam = null): array
    {
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($paginaActual < 1) {
            $paginaActual = 1;
        }

        $totalElementos = 0;
        //method_exists es para verificar si el método existe en el modelo
        if ($countMethod && method_exists($modelo, $countMethod)) {
            $totalElementos = $modelo->{$countMethod}($countParam);
        } else {
            $totalElementos = $modelo->contar();
        }

        $totalPaginas = (int) max(1, ceil($totalElementos / self::TAREAS_POR_PAGINA));

        return [
            'paginaActual' => $paginaActual,
            'totalElementos' => $totalElementos,
            'totalPaginas' => $totalPaginas,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Finanzas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FinanzasController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:ingreso,gasto',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 400);
        }

        $movimiento = Finanzas::create([
            'user_id' => Auth::id(),
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'fecha_registro' => $request->fecha,
        ]);

        return response()->json(['success' => true, 'id' => $movimiento->id]);
    }

    public function list()
    {
        $items = Finanzas::where('user_id', Auth::id())
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function summary(Request $request)
    {
        $anio = $request->input('anio', date('Y'));
        
        $resumen = Finanzas::where('user_id', Auth::id())
            ->whereYear('fecha_registro', $anio)
            ->selectRaw('SUM(CASE WHEN tipo = "ingreso" THEN monto ELSE 0 END) AS ingresos')
            ->selectRaw('SUM(CASE WHEN tipo = "gasto" THEN monto ELSE 0 END) AS gastos')
            ->first();

        $ingresos = (float)($resumen->ingresos ?? 0);
        $gastos = (float)($resumen->gastos ?? 0);
        $ahorro = $ingresos - $gastos;

        return response()->json([
            'success' => true,
            'summary' => [
                'ingresos' => $ingresos,
                'gastos' => $gastos,
                'ahorro' => $ahorro
            ]
        ]);
    }

    public function monthly(Request $request)
    {
        $anio = $request->input('anio', date('Y'));

        $items = Finanzas::where('user_id', Auth::id())
            ->whereYear('fecha_registro', $anio)
            ->get();

        $mensual = array_fill(1, 12, ['ingresos' => 0.0, 'gastos' => 0.0]);

        foreach ($items as $item) {
            $mes = (int)date('n', strtotime($item->fecha_registro));
            if ($item->tipo === 'ingreso') {
                $mensual[$mes]['ingresos'] += (float)$item->monto;
            } else {
                $mensual[$mes]['gastos'] += (float)$item->monto;
            }
        }

        return response()->json(['success' => true, 'monthly' => $mensual]);
    }
}

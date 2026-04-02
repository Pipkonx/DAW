<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Authorize (Asset must belong to user)
        if ($asset->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ticker' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:7',
            'sector' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'currency_code' => 'nullable|string|size:3',
        ]);

        $asset->update($validated);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) {
            abort(403);
        }

        // Eliminar todas las transacciones asociadas
        $asset->transactions()->delete();

        // Eliminar el activo
        $asset->delete();

        return redirect()->back()->with('success', 'Activo y sus operaciones eliminados correctamente.');
    }

    /**
     * Elimina múltiples activos.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:assets,id'
        ]);

        $count = 0;
        foreach ($request->ids as $id) {
            $asset = Asset::find($id);
            if ($asset && $asset->user_id === Auth::id()) {
                $asset->transactions()->delete();
                $asset->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' activos y sus operaciones eliminados correctamente.');
    }
}

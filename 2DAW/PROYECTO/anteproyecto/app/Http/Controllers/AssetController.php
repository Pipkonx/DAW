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
}

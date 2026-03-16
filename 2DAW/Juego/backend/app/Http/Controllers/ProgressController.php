<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Get user progress.
     */
    public function index()
    {
        $progress = Auth::user()->progress;
        
        if (!$progress) {
            return response()->json([
                'current_mission' => 0,
                'unlocked_technologies' => [],
                'resources' => ['money' => 100, 'wood' => 0, 'water' => 50],
                'stats' => ['harvested' => 0, 'planted' => 0]
            ]);
        }

        return response()->json($progress);
    }

    /**
     * Save or update user progress.
     */
    public function store(Request $request)
    {
        $request->validate([
            'current_mission' => 'required|integer',
            'unlocked_technologies' => 'array',
            'resources' => 'array',
            'stats' => 'array'
        ]);

        $progress = Progress::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'current_mission' => $request->current_mission,
                'unlocked_technologies' => $request->unlocked_technologies,
                'resources' => $request->resources,
                'stats' => $request->stats,
            ]
        );

        return response()->json($progress);
    }
}

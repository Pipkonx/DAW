<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Asset;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        // 1. Crecimiento de Usuarios (últimos 30 días)
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 2. Actividad de Contenido (Posts vs Comentarios)
        $postActivity = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $commentActivity = Comment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Activos Más Populares (Menciones en posts)
        $popularAssets = Asset::withCount('posts')
            ->whereHas('posts')
            ->orderBy('posts_count', 'desc')
            ->take(10)
            ->get()
            ->map(fn($asset) => [
                'ticker' => $asset->ticker,
                'name' => $asset->name,
                'mentions' => $asset->posts_count
            ]);

        // 4. Volúmenes de Transacciones (por tipo)
        $transactionDistribution = Transaction::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        return Inertia::render('Admin/Analytics', [
            'metrics' => [
                'user_growth' => $userGrowth,
                'post_activity' => $postActivity,
                'comment_activity' => $commentActivity,
                'popular_assets' => $popularAssets,
                'tx_distribution' => $transactionDistribution,
                'totals' => [
                    'active_users_24h' => User::where('updated_at', '>=', Carbon::now()->subDay())->count(),
                    'new_posts_24h' => Post::where('created_at', '>=', Carbon::now()->subDay())->count(),
                    'total_volume' => Transaction::sum('amount'), // Ojo: esto suma cantidades de activos, simplificado por ahora
                ]
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Post::count();
        $negative = Post::where('sentiment', 'negative')->count();

        $stats = [
            'total' => $total,
            'negative_share' => $total ? (int) round($negative / $total * 100) : 0,
            'open_alerts' => Alert::where('status', 'open')->count(),
            'critical' => Post::where('severity', 'high')->where('status', 'new')->count(),
        ];

        // Соңғы 14 күн
        $start = now()->subDays(13)->startOfDay();
        $byDay = Post::where('published_at', '>=', $start)->get(['published_at', 'sentiment'])
            ->groupBy(fn($p) => $p->published_at->format('Y-m-d'));

        $labels = $totals = $negatives = [];
        for ($i = 0; $i < 14; $i++) {
            $d = $start->copy()->addDays($i);
            $group = $byDay->get($d->format('Y-m-d'), collect());
            $labels[] = $d->format('d.m');
            $totals[] = $group->count();
            $negatives[] = $group->where('sentiment', 'negative')->count();
        }
        $dayChart = compact('labels', 'totals', 'negatives');

        $cats = Category::withCount('posts')->orderByDesc('posts_count')->get();
        $catChart = [
            'labels' => $cats->pluck('name'),
            'values' => $cats->pluck('posts_count'),
            'colors' => $cats->pluck('color'),
        ];

        $regions = Post::select('region', DB::raw('count(*) as c'))
            ->groupBy('region')->orderByDesc('c')->limit(8)->pluck('c', 'region');
        $regionChart = ['labels' => $regions->keys(), 'values' => $regions->values()];

        $critical = Post::with('categories')->where('severity', 'high')->where('status', 'new')
            ->latest('published_at')->limit(6)->get();
        $alerts = Alert::with('category')->where('status', 'open')->latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'dayChart', 'catChart', 'regionChart', 'critical', 'alerts'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $rows = DB::table('category_post')
            ->join('posts', 'posts.id', '=', 'category_post.post_id')
            ->join('categories', 'categories.id', '=', 'category_post.category_id')
            ->whereBetween('posts.published_at', [$from, $to])
            ->selectRaw('categories.id as cid, categories.name as name, posts.sentiment as sentiment, count(*) as c')
            ->groupBy('categories.id', 'categories.name', 'posts.sentiment')
            ->get();

        $matrix = [];
        foreach ($rows as $r) {
            $matrix[$r->cid]['name'] = $r->name;
            $matrix[$r->cid][$r->sentiment] = $r->c;
        }
        foreach ($matrix as &$m) {
            $m += ['negative' => 0, 'neutral' => 0, 'positive' => 0];
            $m['total'] = $m['negative'] + $m['neutral'] + $m['positive'];
            $m['neg_share'] = $m['total'] ? (int) round($m['negative'] / $m['total'] * 100) : 0;
        }
        unset($m);
        uasort($matrix, fn($a, $b) => $b['negative'] <=> $a['negative']);

        $regions = Post::whereBetween('published_at', [$from, $to])
            ->select('region', DB::raw("count(*) as total"),
                DB::raw("sum(case when sentiment = 'negative' then 1 else 0 end) as negative"))
            ->groupBy('region')->orderByDesc('negative')->get();

        $summary = [
            'total' => Post::whereBetween('published_at', [$from, $to])->count(),
            'avg_severity' => (int) round(Post::whereBetween('published_at', [$from, $to])->avg('severity_score') ?? 0),
            'avg_sentiment' => round(Post::whereBetween('published_at', [$from, $to])->avg('sentiment_score') ?? 0, 2),
        ];

        $chart = [
            'labels' => collect($matrix)->pluck('name')->values(),
            'negative' => collect($matrix)->pluck('negative')->values(),
            'neutral' => collect($matrix)->pluck('neutral')->values(),
            'positive' => collect($matrix)->pluck('positive')->values(),
        ];

        return view('reports.index', [
            'matrix' => $matrix, 'regions' => $regions, 'summary' => $summary, 'chart' => $chart,
            'from' => $from->toDateString(), 'to' => $to->toDateString(),
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $sentiment = config('socialmonitor.sentiment');
        $severity = config('socialmonitor.severity');

        return response()->streamDownload(function () use ($from, $to, $sentiment, $severity) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // Excel үшін UTF-8 BOM
            fputcsv($out, ['ID', 'Уақыты', 'Дереккөз', 'Өңір', 'Тақырып', 'Санат', 'Сезім', 'Ауырлық', 'Балл', 'Күйі'], ';');

            Post::with(['source', 'categories'])
                ->whereBetween('published_at', [$from, $to])
                ->chunkById(200, function ($posts) use ($out, $sentiment, $severity) {
                    foreach ($posts as $p) {
                        fputcsv($out, [
                            $p->id, $p->published_at->format('Y-m-d H:i'), $p->source->name, $p->region, $p->title,
                            $p->primary()?->name, $sentiment[$p->sentiment] ?? $p->sentiment,
                            $severity[$p->severity] ?? $p->severity, $p->severity_score,
                            config('socialmonitor.post_status.' . $p->status),
                        ], ';');
                    }
                });
            fclose($out);
        }, 'social_report_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function range(Request $request): array
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);

        $from = Carbon::parse($request->input('from', now()->subDays(29)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        return [$from, $to];
    }
}

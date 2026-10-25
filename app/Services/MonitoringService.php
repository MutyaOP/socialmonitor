<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Category;

/**
 * Мониторинг: соңғы 24 сағаттағы теріс мәтіндер санын алдыңғы 7 күннің
 * тәуліктік орташасымен салыстырып, күрт өсу болса ескерту (alert) жасайды.
 */
class MonitoringService
{
    /** @return int жаңа жасалған / жаңартылған ескертулер саны */
    public function scan(): int
    {
        $min = config('socialmonitor.alert_min_count');
        $growth = config('socialmonitor.alert_growth');
        $changed = 0;

        foreach (Category::all() as $cat) {
            $negative = fn() => $cat->posts()->where('posts.sentiment', 'negative');

            $recent = $negative()->where('posts.published_at', '>=', now()->subDay())->count();
            $prev = $negative()
                ->whereBetween('posts.published_at', [now()->subDays(8), now()->subDay()])
                ->count();
            $baseline = round($prev / 7, 2);

            if ($recent < $min || $recent < max($baseline * $growth, $baseline + 2)) {
                continue;
            }

            $ratio = $baseline > 0 ? $recent / $baseline : $recent;
            $level = ($ratio >= 3 || $recent >= 8) ? 'high' : 'medium';
            $message = "«{$cat->name}» бойынша соңғы 24 сағатта {$recent} теріс мәтін (тәуліктік орташа: {$baseline}).";

            $alert = Alert::where('category_id', $cat->id)->where('status', 'open')->first();
            if ($alert) {
                $alert->update(['level' => $level, 'recent_count' => $recent, 'baseline' => $baseline, 'message' => $message]);
            } else {
                Alert::create([
                    'category_id' => $cat->id, 'level' => $level, 'recent_count' => $recent,
                    'baseline' => $baseline, 'message' => $message, 'status' => 'open',
                ]);
            }
            $changed++;
        }

        return $changed;
    }
}

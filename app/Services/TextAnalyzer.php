<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Collection;

/**
 * Rule-based мәтін талдау модулі (қазақ + орыс тілдері):
 *  1) санаттарға жіктеу — кілт сөздер сөздігі бойынша (салмақпен);
 *  2) сезім (sentiment) — полярлық сөздік бойынша, tanh-пен -1..1 аралығына келтіріледі;
 *  3) шұғылдық / ауырлық балы (0–100);
 *  4) негізгі сөздер (жиілік бойынша).
 */
class TextAnalyzer
{
    private ?Collection $categories = null;

    public function refresh(): void
    {
        $this->categories = null;
    }

    private function categories(): Collection
    {
        return $this->categories ??= Category::with('keywords')->get();
    }

    public function tokens(string $text): array
    {
        return preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    private function countStem(array $tokens, string $stem): int
    {
        $stem = mb_strtolower($stem);
        $n = 0;
        foreach ($tokens as $t) {
            if (str_starts_with($t, $stem)) {
                $n++;
            }
        }

        return $n;
    }

    public function analyze(string $title, string $body): array
    {
        $tokens = $this->tokens($title . ' ' . $body);

        // 1) санаттар
        $cats = [];
        foreach ($this->categories() as $c) {
            $score = 0;
            $matched = [];
            foreach ($c->keywords as $k) {
                $n = $this->countStem($tokens, $k->word);
                if ($n > 0) {
                    $score += $k->weight * min($n, 3);
                    $matched[] = $k->word;
                }
            }
            if ($score > 0) {
                $cats[$c->id] = ['category' => $c, 'score' => $score, 'matched' => $matched];
            }
        }
        uasort($cats, fn($a, $b) => $b['score'] <=> $a['score']);
        $primaryId = array_key_first($cats);

        // 2) сезім
        $lexicon = config('socialmonitor.lexicon');
        $raw = 0;
        $neg = [];
        $pos = [];
        foreach ($tokens as $t) {
            foreach ($lexicon as $stem => $polarity) {
                if (str_starts_with($t, $stem)) {
                    $raw += $polarity;
                    if ($polarity < 0) {
                        $neg[$t] = true;
                    } else {
                        $pos[$t] = true;
                    }
                    break;
                }
            }
        }
        $sentimentScore = round(tanh($raw / 4), 2);
        $sentiment = $sentimentScore <= -0.25 ? 'negative' : ($sentimentScore >= 0.25 ? 'positive' : 'neutral');

        // 3) шұғылдық және ауырлық
        $urgent = [];
        foreach ($tokens as $t) {
            foreach (config('socialmonitor.urgent') as $stem) {
                if (str_starts_with($t, $stem)) {
                    $urgent[$t] = true;
                    break;
                }
            }
        }
        $topCatScore = $primaryId ? $cats[$primaryId]['score'] : 0;
        $severityScore = (int) round(
            max(0, -$sentimentScore) * 45
            + min(count($urgent), 3) / 3 * 35
            + min($topCatScore, 6) / 6 * 20
        );
        $severity = $severityScore >= 60 ? 'high' : ($severityScore >= 35 ? 'medium' : 'low');

        // 4) негізгі сөздер
        $stop = config('socialmonitor.stopwords');
        $freq = [];
        foreach ($tokens as $t) {
            if (mb_strlen($t) >= 4 && !ctype_digit($t) && !in_array($t, $stop, true)) {
                $freq[$t] = ($freq[$t] ?? 0) + 1;
            }
        }
        arsort($freq);

        return [
            'categories' => $cats,
            'primary' => $primaryId,
            'sentiment_score' => $sentimentScore,
            'sentiment' => $sentiment,
            'severity_score' => min($severityScore, 100),
            'severity' => $severity,
            'top_words' => array_slice(array_keys($freq), 0, 5),
            'negative_hits' => array_keys($neg),
            'positive_hits' => array_keys($pos),
            'urgent_hits' => array_keys($urgent),
            'token_count' => count($tokens),
        ];
    }

    /** Мәтінді талдап, нәтижесін базаға жазады (өрістер + санаттар). */
    public function apply(Post $post): array
    {
        $r = $this->analyze($post->title, $post->body);

        $post->update([
            'sentiment_score' => $r['sentiment_score'],
            'sentiment' => $r['sentiment'],
            'severity_score' => $r['severity_score'],
            'severity' => $r['severity'],
            'top_words' => $r['top_words'],
        ]);

        $sync = [];
        foreach ($r['categories'] as $id => $c) {
            $sync[$id] = [
                'score' => $c['score'],
                'matched' => implode(', ', $c['matched']),
                'is_primary' => $id === $r['primary'],
            ];
        }
        $post->categories()->sync($sync);

        return $r;
    }
}

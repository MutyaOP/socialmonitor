<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Keyword;
use App\Services\TextAnalyzer;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $items = Category::withCount(['posts', 'keywords'])
            ->when($request->q, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')->get();

        return view('categories.index', compact('items'));
    }

    public function create()
    {
        return view('categories.form', ['item' => new Category(['color' => '#6366f1'])]);
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return redirect()->route('categories.show', $category)->with('success', 'Санат қосылды. Енді кілт сөздер қосыңыз.');
    }

    public function show(Category $category)
    {
        $category->load('keywords');
        $posts = $category->posts()->latest('posts.published_at')->limit(10)->get();
        $sentiments = $category->posts()->selectRaw('posts.sentiment as s, count(*) as c')
            ->groupBy('posts.sentiment')->pluck('c', 's');

        return view('categories.show', compact('category', 'posts', 'sentiments'));
    }

    public function edit(Category $category)
    {
        return view('categories.form', ['item' => $category]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.show', $category)->with('success', 'Өзгерістер сақталды.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Санат жойылды.');
    }

    public function storeKeyword(Request $request, Category $category)
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'max:60'],
            'weight' => ['required', 'integer', 'between:1,3'],
        ]);
        $data['word'] = mb_strtolower(trim($data['word']));

        $category->keywords()->updateOrCreate(['word' => $data['word']], ['weight' => $data['weight']]);

        return back()->with('success', 'Кілт сөз сақталды. Бұрынғы мәтіндерді қайта талдау үшін «Қайта талдау» батырмасын басыңыз.');
    }

    public function destroyKeyword(Keyword $keyword)
    {
        $keyword->delete();

        return back()->with('success', 'Кілт сөз жойылды.');
    }

    /** Санат сөздігі өзгергеннен кейін барлық мәтінді қайта талдау. */
    public function reanalyzeAll(TextAnalyzer $analyzer)
    {
        $analyzer->refresh();
        $n = 0;
        \App\Models\Post::chunkById(100, function ($posts) use ($analyzer, &$n) {
            foreach ($posts as $p) {
                $analyzer->apply($p);
                $n++;
            }
        });

        return back()->with('success', "{$n} мәтін қайта талданды.");
    }
}

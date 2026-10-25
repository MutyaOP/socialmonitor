<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Source;
use App\Services\TextAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $items = Post::with(['source', 'categories'])
            ->when($request->q, fn($q, $s) => $q->where(
                fn($w) => $w->where('title', 'like', "%{$s}%")->orWhere('body', 'like', "%{$s}%")
            ))
            ->when($request->category_id, fn($q, $v) => $q->whereHas('categories', fn($c) => $c->where('categories.id', $v)))
            ->when($request->sentiment, fn($q, $v) => $q->where('sentiment', $v))
            ->when($request->severity, fn($q, $v) => $q->where('severity', $v))
            ->when($request->source_id, fn($q, $v) => $q->where('source_id', $v))
            ->when($request->region, fn($q, $v) => $q->where('region', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->from, fn($q, $v) => $q->whereDate('published_at', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('published_at', '<=', $v))
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        return view('posts.index', [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'sources' => Source::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('posts.form', [
            'item' => new Post(['status' => 'new', 'region' => config('socialmonitor.regions')[0]]),
            'sources' => Source::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(PostRequest $request, TextAnalyzer $analyzer)
    {
        $post = Post::create($request->validated() + ['user_id' => $request->user()->id]);
        $analyzer->apply($post);

        return redirect()->route('posts.show', $post)->with('success', 'Мәтін қосылып, талданды.');
    }

    public function show(Post $post)
    {
        $post->load(['source', 'user', 'categories']);
        $analysis = [
            'categories' => $post->categories->sortByDesc('pivot.score'),
        ];

        return view('posts.show', compact('post', 'analysis'));
    }

    public function edit(Post $post)
    {
        return view('posts.form', [
            'item' => $post,
            'sources' => Source::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(PostRequest $request, Post $post, TextAnalyzer $analyzer)
    {
        $post->update($request->validated());
        $analyzer->apply($post);

        return redirect()->route('posts.show', $post)->with('success', 'Өзгерістер сақталып, мәтін қайта талданды.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Мәтін жойылды.');
    }

    public function status(Request $request, Post $post)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('socialmonitor.post_status')))],
        ]);
        $post->update($data);

        return back()->with('success', 'Күйі жаңартылды.');
    }

    public function reanalyze(Post $post, TextAnalyzer $analyzer)
    {
        $analyzer->apply($post);

        return back()->with('success', 'Мәтін қайта талданды.');
    }
}

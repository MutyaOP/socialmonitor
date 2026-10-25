<?php

namespace App\Http\Controllers;

use App\Http\Requests\SourceRequest;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function index(Request $request)
    {
        $items = Source::withCount('posts')
            ->when($request->q, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('sources.index', compact('items'));
    }

    public function create()
    {
        return view('sources.form', ['item' => new Source(['type' => 'news', 'is_active' => true])]);
    }

    public function store(SourceRequest $request)
    {
        Source::create($this->data($request));

        return redirect()->route('sources.index')->with('success', 'Дереккөз қосылды.');
    }

    public function edit(Source $source)
    {
        return view('sources.form', ['item' => $source]);
    }

    public function update(SourceRequest $request, Source $source)
    {
        $source->update($this->data($request));

        return redirect()->route('sources.index')->with('success', 'Өзгерістер сақталды.');
    }

    public function destroy(Source $source)
    {
        $source->delete();

        return redirect()->route('sources.index')->with('success', 'Дереккөз жойылды.');
    }

    private function data(SourceRequest $request): array
    {
        return array_merge($request->validated(), ['is_active' => $request->boolean('is_active')]);
    }
}

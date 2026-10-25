<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyzeRequest;
use App\Services\TextAnalyzer;

class AnalyzeController extends Controller
{
    public function index()
    {
        return view('analyze.index', ['result' => null]);
    }

    /** Мәтінді сақтамай, жылдам талдау (демонстрация). */
    public function analyze(AnalyzeRequest $request, TextAnalyzer $analyzer)
    {
        $result = $analyzer->analyze($request->input('title', ''), $request->input('body'));

        return view('analyze.index', ['result' => $result]);
    }
}

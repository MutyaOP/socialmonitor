<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_id' => ['required', 'exists:sources,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:10', 'max:10000'],
            'region' => ['required', Rule::in(config('socialmonitor.regions'))],
            'published_at' => ['required', 'date', 'before_or_equal:now'],
            'status' => ['required', Rule::in(array_keys(config('socialmonitor.post_status')))],
        ];
    }

    public function attributes(): array
    {
        return [
            'source_id' => 'дереккөз', 'title' => 'тақырып', 'body' => 'мәтін',
            'region' => 'өңір', 'published_at' => 'жарияланған уақыты', 'status' => 'күйі',
        ];
    }
}

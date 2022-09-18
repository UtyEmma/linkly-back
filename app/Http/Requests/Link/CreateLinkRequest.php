<?php

namespace App\Http\Requests\Link;

use Illuminate\Foundation\Http\FormRequest;

class CreateLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string',
            'url' => 'nullable|url',
            'shorturl' => 'nullable|string',
            'status' => 'required|string|in:draft,published'
        ];
    }
}

<?php

namespace App\Http\Requests\Links;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
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
            'status' => 'nullable|string|in:draft,published',
            'position' => 'nullable|numeric',
            'thumbnail' => 'nullable|in:image,icon',
            'icon' => 'required_with:thumbnail'
        ];
    }
}

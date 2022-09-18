<?php

namespace App\Http\Requests\Page;

use App\Library\Response;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
            'title' => 'required|string',
            'slug' => 'required|string',
            'desc' => 'string',
            'logo' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_desc' => 'nullable|string',
            'meta_tags' => 'nullable|string'
        ];
    }
}

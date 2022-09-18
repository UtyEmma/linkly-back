<?php

namespace App\Http\Requests\Links;

use Illuminate\Foundation\Http\FormRequest;

class CreateClickRequest extends FormRequest
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
            'link_id' => 'required|string|exists:links,unique_id',
            'session_id' => 'required|string|exists:sessions,unique_id',
        ];
    }
}

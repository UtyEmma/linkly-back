<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
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
            'new_password' => ['required', 'confirmed', Password::defaults()],
            'current_password' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'new_password' => 'New Password',
            'current_password' => 'Current Password'
        ];
    }
}

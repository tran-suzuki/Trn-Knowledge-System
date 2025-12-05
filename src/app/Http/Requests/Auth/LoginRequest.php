<?php

namespace App\Http\Requests\Auth;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[A-Za-z0-9._\-@]+$/',
                'email:filter',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],
            'remember' => ['nullable', 'boolean'],
        ];
    }
}

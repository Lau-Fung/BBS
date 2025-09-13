<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Admin'); // or can('users.create')
    }

    public function rules(): array
    {
        return [
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['nullable','string','min:8'], // nullable if you’ll send reset link
            'roles'    => ['nullable','array'],
            'roles.*'  => ['exists:roles,id'],
            'verified' => ['nullable','boolean'],
            'send_reset_link' => ['nullable','boolean'],
        ];
    }
}

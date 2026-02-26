<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => "sometimes|required|email|unique:users,email,{$userId}",
            'password' => 'sometimes|nullable|string|min:6|confirmed',
            'role'     => 'sometimes|required|string|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Name is required.',
            'email.required'    => 'Email is required.',
            'email.email'       => 'Email must be a valid email address.',
            'email.unique'      => 'Email already exists.',
            'password.min'      => 'Password must be at least 6 characters.',
            'password.confirmed'=> 'Password confirmation does not match.',
            'role.required'     => 'Role is required.',
            'role.exists'       => 'Selected role does not exist.',
        ];
    }
}

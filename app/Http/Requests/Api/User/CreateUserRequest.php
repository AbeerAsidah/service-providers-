<?php

namespace App\Http\Requests\Api\User;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255', 
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|string|min:8',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required'
        ];
    }

}

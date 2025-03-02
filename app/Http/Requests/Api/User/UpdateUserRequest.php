<?php

namespace App\Http\Requests\Api\User;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->route('id'),
            'phone_number' => 'nullable|unique:users,phone_number,' . $this->route('id'),
            'password' => 'nullable|string|min:8',
            'identity_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'nullable'

        ];
    }

}

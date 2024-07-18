<?php

namespace App\Http\Requests\Api;

use App\Rules\UserRoleEmail;
use App\Traits\HandlesValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SendVerificationCodeRequest extends FormRequest
{
    use HandlesValidationErrorsTrait;

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

        return auth('sanctum')->user() ? [] :
            [
                'email' => ['required', 'email', new UserRoleEmail],
            ];
    }
}

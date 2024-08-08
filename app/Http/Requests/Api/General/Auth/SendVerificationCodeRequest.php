<?php

namespace App\Http\Requests\Api\General\Auth;

use App\Rules\AdminRoleEmail;
use App\Rules\UserRoleEmail;
use App\Traits\HandlesValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;

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
                'email' => ['required', 'email',(str_contains($this->url(), 'admin'))? new AdminRoleEmail :new UserRoleEmail],
            ];
    }
}

<?php

namespace App\Http\Requests\Api\User;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;

class GetUsersRequest extends FormRequest
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
            'role' => 'in:' . implode(',', array_values(Constants::ROLES))
        ];
    }

}

<?php

namespace App\Http\Requests\Api\Section;

use App\Constants\Constants;

use App\Traits\HandlesValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SectionRequest extends FormRequest
{
    use HandlesValidationErrorsTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
            $this->merge([
                'type' => $this->route('type')??'super',
            ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return array_merge(
            Constants::SECTIONS_TYPES[$this->route("type") ?? 'super']['rules'][$this->route('section') ? 'update' : 'create'],
            ['type' => 'in:' . implode(',', array_keys(Constants::SECTIONS_TYPES))]
        );
    }
}

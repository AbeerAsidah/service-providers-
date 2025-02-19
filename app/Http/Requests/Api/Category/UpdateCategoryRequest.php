<?php

namespace App\Http\Requests\Api\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
    * Prepare the data for validation.
    */
    protected function prepareForValidation(): void
    {
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'ar_name' => 'nullable|string|max:255', 
            'en_name' => 'nullable|string|max:255',
            'ar_description' => 'nullable|string|max:1000', 
            'en_description' => 'nullable|string|max:1000',     
           ];
    }
}

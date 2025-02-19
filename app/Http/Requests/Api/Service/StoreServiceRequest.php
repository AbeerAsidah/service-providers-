<?php

namespace App\Http\Requests\Api\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'service_provider_id' => 'nullable|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'ar_name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
            'ar_description' => 'nullable|string',
            'en_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'complete_time' => 'required|integer|min:1',
            'status' => 'required|in:active,disabled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }
}

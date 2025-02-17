<?php

namespace App\Http\Requests\Api\Service;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكن تعديلها حسب الصلاحيات
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'ar_name' => 'nullable|string|max:255',
            'en_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'complete_time' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}

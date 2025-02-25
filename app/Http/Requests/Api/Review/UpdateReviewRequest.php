<?php

namespace App\Http\Requests\Api\Review;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::id() === $this->review->user_id; // يجب أن يكون المستخدم صاحب التقييم
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
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ];
    }
}

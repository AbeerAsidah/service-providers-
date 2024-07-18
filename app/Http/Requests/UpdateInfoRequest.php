<?php

namespace App\Http\Requests;

use App\Rules\OneOrNone;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="UpdateInfoRequest",
 *     type="object",
 *     title="Update Info Request",
 *     required={"key"},
 *     @OA\Property(
 *         property="key",
 *         type="string",
 *         description="Key of the info entry",
 *         example="exampleKey"
 *     ),
 *     @OA\Property(
 *         property="super_key",
 *         type="string",
 *         description="Super key value",
 *         example="exampleSuperKey"
 *     ),
 *     @OA\Property(
 *         property="new_super_key",
 *         type="string",
 *         description="Super key value",
 *         example="exampleSuperKey"
 *     ),
 *     @OA\Property(
 *         property="en_value",
 *         type="string",
 *         description="English translation of info value",
 *         example="Value in English"
 *     ),
 *     @OA\Property(
 *         property="ar_value",
 *         type="string",
 *         description="Arabic translation of info value",
 *         example="القيمة باللغة العربية"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         format="binary",
 *         description="Image file"
 *     )
 * )
 */
class UpdateInfoRequest extends FormRequest
{
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
        return [
            'key' => ['required' , 'string' , Rule::exists('infos' , 'key')] , 
            'super_key' => ['string' , 'nullable'] ,
            'en_value' => ['string' , 'nullable' , new OneOrNone('image')],
            'ar_value' => ['string' , 'nullable' , new OneOrNone('image')],
            'image' => ['image' , 'nullable' , new OneOrNone('en_value') , new OneOrNone('ar_value')]
        ];
    }
}

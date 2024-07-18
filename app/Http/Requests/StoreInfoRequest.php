<?php

namespace App\Http\Requests;

use App\Rules\OneOrNone;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreInfoRequest",
 *     type="object",
 *     required={"key", "super_key"},
 *     @OA\Property(property="key", type="string", example="exampleKey"),
 *     @OA\Property(property="super_key", type="string", example="exampleSuperKey"),
 *     @OA\Property(property="en_value", type="string", example="exampleValueEN"),
 *     @OA\Property(property="ar_value", type="string", example="exampleValueAR"),
 *     @OA\Property(property="image", type="string", format="binary")
 * )
 */
class   StoreInfoRequest extends FormRequest
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
            'key' => ['required' , 'string' , 'max:255' , 'unique:infos,key'] ,
            'super_key' => ['required' , 'string' , 'max:255' , /*Rule::unique('infos' , 'super_key')->where('key' , request('key'))*/] ,
            'en_value' => ['required_without:image' , 'string' , 'max:255' , new OneOrNone('image')] ,
            'ar_value' => ['required_without:image' , 'string' , 'max:255' , new OneOrNone('image')] ,
            'image' => ['required_without_all:en_value,ar_value' , new OneOrNone('en_value') , new OneOrNone('ar_value') , 'image'] ,
        ];
    }
}

<?php

namespace App\Http\Requests\Api\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
      public function authorize()
        {
            return true;
        }
    
        public function rules()
        {
            return [
                'user_id' => 'nullable|exists:users,id',
                'amount' => 'required|numeric|min:10', 
                'bank_account' => 'required|string',
            ];
        }
}
    

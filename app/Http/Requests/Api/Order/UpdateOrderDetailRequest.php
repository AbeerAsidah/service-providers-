<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\Constants;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
class UpdateOrderDetailRequest extends FormRequest
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
        $user = Auth::user();
    
        $statuses = Constants::ORDER_STATUSES;
    
        // if ($user->hasRole(Constants::SERVICE_PROVIDER_ROLE)) {
        //     // $statuses = ['PAID', 'DELIVERING'];
        // } 
    
    
        return [
            'status' => 'required|string|in:' . implode(',', $statuses),    
        ];
    }
    
}

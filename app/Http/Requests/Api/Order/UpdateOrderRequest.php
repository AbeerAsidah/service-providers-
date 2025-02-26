<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\Constants;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
class UpdateOrderRequest extends FormRequest
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
    
       // 🔹 تعريف الحالات المسموح بها لكل نوع مستخدم
       if ($user->hasRole(Constants::SERVICE_PROVIDER_ROLE)) {
        // مقدم الخدمة يمكنه إدخال in_progress, completed, أو canceled
        $allowedStatuses = [
            Constants::ORDER_STATUSES[1], // 'in_progress'
            Constants::ORDER_STATUSES[2], // 'completed'
            Constants::ORDER_STATUSES[3]  // 'canceled'
        ];
    } elseif ($user->hasRole(Constants::USER_ROLE)) {
        // المستخدم العادي يمكنه إدخال completed أو canceled فقط
        $allowedStatuses = [
            Constants::ORDER_STATUSES[2], // 'completed'
            Constants::ORDER_STATUSES[3]  // 'canceled'
        ];
    } else {
        // أي مستخدم آخر (مقدم خدمة أو مسؤول) يمكنه إدخال أي حالة متاحة
        $allowedStatuses = Constants::ORDER_STATUSES;
    }
        return [
            'status' => 'required|string|in:' . implode(',', $allowedStatuses),    
        ];
    }
    
}

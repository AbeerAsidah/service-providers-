<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait; 
class Order extends Model
{
    use HasFactory , SoftDeletes;  

    protected $fillable = [
        'user_id' , 'provider_id', 'service_id', 'price', 'quantity', 'total_price', 'status', 'complete_time_unit', 'complete_time'
    ];

   

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::updated(function ($orderDetail) {
    //         if ($orderDetail->status === 'completed') {
    //             $order = $orderDetail->order;

    //             if ($order->orderDetails()->where('status', '!=', 'completed')->count() === 0) {
    //                 $order->update(['status' => 'completed']);
    //             }
    //         }
    //     });
    // }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

//     public static function boot()
// {
//     parent::boot();

//     static::updated(function ($order) {
//         if ($order->status === Constants::ORDER_STATUSES['completed']) { 
//             Transaction::create([
//                 'provider_id' => $order->provider_id,
//                 'type' => 'earning',
//                 'amount' => $order->total_price,
//                 'status' => 'approved'
//             ]);

//             $wallet = $order->provider->wallet;
//             $wallet->increment('balance', $order->total_price);
//         }
//     });
// }

public static function boot()
{
    parent::boot();

    static::updated(function ($order) {
        if ($order->status === Constants::ORDER_STATUSES['completed']) { 
            try {
                if (!$order->provider->wallet) {
                    throw new \Exception(__('messages.wallet_not_found'));
                }

                Transaction::create([
                    'provider_id' => $order->provider_id,
                    'type' => Constants::TRANSACTION_TYPES['earning'],
                    'amount' => $order->total_price,
                    'status' => Constants::TRANSACTION_STATUSES['approved']
                ]);

                app(WalletService::class)->addBalance($order->provider_id, $order->total_price);

            } catch (\Exception $e) {
                \Log::error('Error updating wallet balance: ' . $e->getMessage());
            }
        }
    });
}


}

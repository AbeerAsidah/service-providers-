<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait; 
use App\Services\Wallet\WalletService; 
use App\Services\Review\ReviewService; 
class Service extends Model
{
    use HasFactory , SoftDeletes, HasTranslations;  

    protected $fillable = [
        'service_provider_id', 'category_id', 'name', 'description', 'price', 'complete_time', 'status', 'image'
    ];
    public $translatable = ['name', 'description'];

    protected $appends = ['provider1'];

    public function provider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    protected function getProvider1Attribute()
    {
            if (!$this->relationLoaded('provider')) {
                $this->load('provider');
            }
    
            $provider = $this->getRelation('provider'); 
    
            if (!$provider) {
                return [
                    'provider' => null,
                    'wallet_balance' => null,
                    'provider_avg_rating' => null,
                ];
            }
    
            $walletService = app()->make(\App\Services\Wallet\WalletService::class);
            $reviewService = app()->make(\App\Services\Review\ReviewService::class);
    
            return [
                'provider' => $provider,
                'wallet_balance' => $walletService->getBalance($provider->id),
                'provider_avg_rating' => $reviewService->getUserAverageRating($provider->id),
            ];
    }
    


    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

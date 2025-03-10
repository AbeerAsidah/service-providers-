<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait; 

class Service extends Model
{
    use HasFactory , SoftDeletes, HasTranslations;  

    protected $fillable = [
        'service_provider_id', 'category_id', 'name', 'description', 'price', 'complete_time', 'status', 'image'
    ];
    public $translatable = ['name', 'description'];


    public function provider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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

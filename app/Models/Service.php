<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'name', 'category', 'description', 'price', 'completion_time', 'status'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
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

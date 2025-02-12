<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'balance'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}

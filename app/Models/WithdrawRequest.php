<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'amount', 'status'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}

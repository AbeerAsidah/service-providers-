<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'type', 'amount', 'status', 'bank_account', 'processed_by_admin'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}



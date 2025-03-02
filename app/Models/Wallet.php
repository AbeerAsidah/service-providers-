<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addFunds($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    public function deductFunds($amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception(__('messages.insufficient_balance'));
        }

        $this->balance -= $amount;
        $this->save();
    }
}

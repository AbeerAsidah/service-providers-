<?php
namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;

use Exception;

class WalletService
{
    public function getBalance($userId)
    {
        $user = User::findOrFail($userId);

        if (!$user->wallet) {
            throw new Exception(__('messages.wallet_not_found'));
        }

        return $user->wallet->balance;
    }

    public function requestWithdrawal($userId, $amount, $bankAccount)
    {
        $user = User::findOrFail($userId);
    
        if (!$user->wallet || $user->wallet->balance < $amount) {
            throw new Exception(__('messages.insufficient_balance'));
        }
    
        return $user->transactions()->create([
            'type' => 'withdrawal',
            'amount' => -$amount,
            'status' => 'pending',
            'bank_account' => $bankAccount
        ]);
    }
    
    public function getProviderWithdrawals($userId)
    {
        $user = User::findOrFail($userId);

        return $user->transactions()
            ->where('type', 'withdrawal')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    



    //admin
    public function getAllWithdrawals($status = null, $userId = null)
    {
        $query = Transaction::where('type', 'withdrawal');

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
            $query->where('provider_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }


    public function approveWithdrawal($withdrawalId)
    {
        $withdrawal = Transaction::findOrFail($withdrawalId);
    
        if ($withdrawal->type !== 'withdrawal') {
            throw new Exception(__('messages.invalid_transaction'));
        }
    
        $wallet = $withdrawal->provider->wallet;
    
        $wallet->deductFunds(abs($withdrawal->amount));
    
        $withdrawal->update(['status' => 'approved']);
    
        return $withdrawal;
    }
    

    public function rejectWithdrawal($withdrawalId)
    {
        $withdrawal = Transaction::findOrFail($withdrawalId);
    
        if ($withdrawal->type !== 'withdrawal') {
            throw new Exception(__('messages.invalid_transaction'));
        }
    
        $withdrawal->update(['status' => 'rejected']);
    
        return $withdrawal;
    }
    


    public function addBalance($userId, $amount)
    {
        $user = User::findOrFail($userId);

        if (!$user->wallet) {
            throw new \Exception(__('messages.wallet_not_found'));
        }

        $user->wallet->addFunds($amount);

        return $user->wallet;
    }

    public function updateBalance($userId, $amount)
    {
        $user = User::findOrFail($userId);

        if (!$user->wallet) {
            throw new Exception(__('messages.wallet_not_found'));
        }

        $user->wallet->balance = $amount;
        $user->wallet->save();

        return $user->wallet;
    }

    public function getAllBalances($userId = null)
    {
        $query = Wallet::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->with('user:id,name,username,email')->orderBy('balance', 'desc')->get();
    }


}

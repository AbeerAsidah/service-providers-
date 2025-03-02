<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Wallet\WalletService;
use App\Models\Transaction;
use App\Http\Requests\Api\Wallet\WithdrawRequest;
use App\Http\Requests\Api\Wallet\UpdateBalanceRequest;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function getBalance()
    {
        try {
            $userId = auth()->id();
            $balance = $this->walletService->getBalance($userId);
            return success(['balance' => $balance]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function requestWithdrawal(WithdrawRequest $request)
    {
        try {
            $userId = auth()->id();
            $withdrawal = $this->walletService->requestWithdrawal(
                $userId,
                $request->amount,
                $request->bank_account
            );

            return success(['withdrawal' => $withdrawal], 200, ['message' => __('messages.withdrawal_requested')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getProviderWithdrawals()
    {
        try {
            $userId = auth()->id();
            $withdrawals = $this->walletService->getProviderWithdrawals($userId);
            return success(['withdrawals' => $withdrawals]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getAllWithdrawals(Request $request)
    {
        try {
            $status = $request->input('status', 'pending');
            $user_id = $request->input('provider_id');
            $withdrawals = $this->walletService->getAllWithdrawals($status, $user_id);
            return success(['withdrawals' => $withdrawals]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function approveWithdrawal($withdrawalId)
    {
        try {
            $withdrawal = $this->walletService->approveWithdrawal($withdrawalId);
            return success(['withdrawal' => $withdrawal], 200, ['message' => __('messages.withdrawal_approved')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function rejectWithdrawal($withdrawalId)
    {
        try {
            $withdrawal = $this->walletService->rejectWithdrawal($withdrawalId);
            return success(['withdrawal' => $withdrawal], 200, ['message' => __('messages.withdrawal_rejected')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function addBalance(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:1',
            ]);
    
            $wallet = $this->walletService->addBalance($validated['user_id'], $validated['amount']);
    
            return success(['wallet' => $wallet], 200, ['message' => __('messages.balance_added')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }
    

    public function updateBalance(UpdateBalanceRequest $request, $userId)
    {
        try {
            $wallet = $this->walletService->updateBalance($userId, $request->amount);
            return success(['wallet' => $wallet], 200, ['message' => __('messages.balance_updated')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getAllBalances(Request $request)
    {
        try {
            $balances = $this->walletService->getAllBalances($request->user_id);
            return success(['balances' => $balances], 200, ['message' => __('messages.all_balances_retrieved')]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

}


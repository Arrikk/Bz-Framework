<?php

namespace App\Controllers\Finance\Wallets;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\Finance\FinancePipe;
use App\Models\Finance\Balance;
use App\Models\Finance\Transaction;
use App\Models\Finance\Wallet;
use App\Models\User;
use Core\Http\Res;

class WalletsLogic extends FinancePipe
{
    /**
     * 
     */
    public function balance(User $user, $wallet_id = null)
    {
        
        $inWallets = $this->wallets($wallet_id);
        if ($wallet_id):
             return $this->singleWalletBalance($user, $wallet_id, $inWallets);
        endif;

        $walletBalance = $user->wallets();

        /**
         * Loop through the wallets list from Wallet
         * use the the wallet array object gotten from wallet balance
         * model to filter and get the balance of wallet
         * @param array $inWallet Wallets
         * @param array $walletBalance wallet balance
         */
        $wallet_ref = array_map(function ($wallet) use ($walletBalance) {
            /**
             * From inside the wallet Filter the wallets with their id
             * Map and return wallet balance matching wallet ID of wallet model
             */
            $walletBalance = array_filter($walletBalance,  function ($balance) use ($wallet) {
                return ($balance->wallet_id ?? '') == $wallet->wallet_id;
            }, ARRAY_FILTER_USE_BOTH);
            // append wallet balance to wallet object
            // since walle is an array and can only be found once in an iteration
            // $walletBalance returns an array with index0 as the initial found one
            $wallet->wallet_balance = $walletBalance[0]->wallet_balance ?? 0;
            $wallet->withdrawable_balance = $walletBalance[0]->withdrawable_balance ?? 0;
            $wallet->non_withdrawable_balance = $walletBalance[0]->non_withdrawable_balance ?? 0;
            return $wallet;
        }, $inWallets);

        return $wallet_ref;
    }

    /**
     * 
     */
    public function singleWalletBalance(User $user, $wallet_id, $inWallet)
    {
        $walletBalance = $user->wallet($wallet_id);
        $inWallet->wallet_balance = $walletBalance->wallet_balance ?? 0;
        $inWallet->withdrawable_balance = $walletBalance->withdrawable_balance ?? 0;
        $inWallet->non_withdrawable_balance = $walletBalance->non_withdrawable_balance ?? 0;
        return $inWallet;
    }

    /**
     * 
     */
    public function wallets($wallet_id = null)
    {
        if (!$wallet_id) return Wallet::find();
        $wallet = Wallet::findOne([
            'wallet_id' => $wallet_id
        ]);
        return $wallet ? $wallet : Res::status(404)::error(['wallet' => "Invalid Wallet ID"]);
    }


    public function recordTransactions($data, $type)
    {
        return Transaction::use('transactions')::dump([
            '_id' => GenerateKey(),
            'user_id' => $data->user_id,
            'wallet_id' => $data->wallet_id,
            'transaction_type' => $type,
            'transaction_amount' => $data->amount ?? 0,
            'balance_before' => $data->balance_before ?? 0,
            'balance_after' => $data->balance_after ?? 0,
            'transaction_reference' => '',
            'transaction_meta' => $data->meta !== null ? serialize($data->meta) : null,
            'transaction_status' => $data->transaction_status ?? ''
        ]);
    }

    public function transact($balance, $user, $wallet_id,  $amount, $newBalance, $type, $metaData = null)
    {
        $credit = (array) Balance::transact($user->id, $wallet_id, $newBalance);
        $tx = $this->recordTransactions((object)array_merge($credit, [
            'transaction_status' => 'completed',
            'balance_before' => $balance->wallet_balance,
            'amount' => $amount,
            'balance_after' => $newBalance,
            'meta' => $metaData
        ]), $type);
        Res::json($tx);
    }
}

<?php

namespace App\Controllers\Finance\Wallets;

use App\Models\Finance\Balance;
use App\Models\Finance\Wallet;
use Core\Http\Res;

class Wallets extends WalletsLogic
{

    /**
     * 
     */
    public function get()
    {
        Res::json($this->wallets());
    }

    /**
     * 
     */
    public function create($req)
    {
        $createData = $this->createWalletPipe($req);
        Res::json(Wallet::dump((array) $createData));
    }

    /**
     * 
     */
    public function _walletBalance()
    {
        Res::json($this->balance($this->user, 'usd'));
    }

    public function _credit($body)
    {
        $data = $this->creditPipe($body);
        $balance = $this->balance($this->user, $data->wallet_id);
        $amount = $balance->wallet_balance + $data->amount;
        $credit = (array) Balance::transact($this->user->id, $data->wallet_id, $amount);
        $this->transact($balance, $this->user, $data->wallet_id, $amount, 'credit');
    }

    public function _debit($body)
    {
        $data = $this->creditPipe($body);
        $balance = $this->balance($this->user, $data->wallet_id);

        Balance::isSufficient($balance->wallet_balance, $data->amount);

        $amount = $balance->wallet_balance - $data->amount;
        
        $this->transact($balance, $this->user, $data->wallet_id, $amount, 'debit');
    }
}

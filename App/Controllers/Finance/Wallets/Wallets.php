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
        $wallet = Wallet::findOne(['wallet_id' => $req->wallet_id]);
        if ($wallet) :
            unset($createData->_id);
            $wallet = $wallet->modify( (array) $createData);
            Res::json($wallet);
        endif;
        $createData = (Wallet::dump((array) $createData));
        Res::json($createData);
    }

    /**
     * 
     */
    public function _walletBalance()
    {
        Res::json($this->balance($this->user,  null));
    }

    public function _credit($body)
    {
        $data = $this->creditPipe($body);
        $balance = $this->balance($this->user, $data->wallet_id);
        $amount = $data->amount;
        $newBalance = $balance->wallet_balance + $data->amount;
        $credit = (array) Balance::transact($this->user->id, $data->wallet_id, $newBalance);
        $this->transact($balance, $this->user, $data->wallet_id, $amount, $newBalance, 'credit', $data->meta_data);
    }

    public function _debit($body)
    {
        $data = $this->creditPipe($body);
        $balance = $this->balance($this->user, $data->wallet_id);

        Balance::isSufficient($balance->wallet_balance, $data->amount);

        $amount = $data->amount;
        $newBalance = $balance->wallet_balance - $data->amount;

        $this->transact($balance, $this->user, $data->wallet_id, $amount, $newBalance, 'debit');
    }
}

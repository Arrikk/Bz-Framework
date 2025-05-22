<?php
namespace App\Models;

use App\Models\Finance\Transaction;
use App\Models\Finance\Wallet;
use Core\Model\Model;
use Core\Traits\User\Extended;
use Core\Traits\User\Help;

class User extends Model
{
    use Help, Extended;

    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'user_id', 'balances');
    }

    public function wallet($wallet_id)
    {
        return $this::use('balances')->findOne([
            'wallet_id' => $wallet_id,
            'and.user_id' => $this->id
        ]);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
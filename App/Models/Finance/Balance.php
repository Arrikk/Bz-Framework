<?php
namespace App\Models\Finance;

use App\Models\Finance\Wallet;
use Core\Http\Res;
use Core\Model\Model;

class Balance extends Model
{
    public static function get_wallet_balance($userID, $walletID)
    {
        // if(!Wallet::findOne(['wallet_id' => $walletID])) return Res::status(400)->error([
        //     'message' => "Invalid Wallet ID",
        //     'walletID' => $walletID
        // ]);

        $wallet = Wallet::findOne([
            '$.left' => 'balances AS b',
            '$.on' => 'wallets.wallet_id = b.wallet_id',
            'user_id' => $userID,
            '$.and' => "wallets.wallet_id = '$walletID'"
        ]);

        return $wallet;

    }

    public static function isSufficient($walletBalance, $amount)
    {

        if($walletBalance < $amount || $walletBalance < 0) Res::status(400)::error([
            'message' => "Insufficient Balance",
            'balance' => $walletBalance,
            'amount' => $amount
        ]);
    }

    public static function transact($userID, $walletID, $amount)
    {
        $wallet = self::findOne(['user_id' => $userID, 'and.wallet_id' => $walletID]);

        if($wallet)return self::findAndUpdate([
            'user_id' => $userID,
            'and.wallet_id' => $walletID,
        ], ['wallet_balance' => $amount, 'updated_at' => CURRENT_DATE]);

        return self::dump([
            'wallet_id' => $walletID,
            'user_id' => $userID,
            'wallet_balance' => $amount,
        ]);
    }

}
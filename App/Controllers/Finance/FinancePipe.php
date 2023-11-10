<?php

namespace App\Controllers\Finance;

use App\Controllers\Authenticated\Authenticated;
use Core\Pipes\Pipes;

class FinancePipe extends Authenticated
{
    public function createWalletPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            '_id' => GenerateKey(30, 50),
            'wallet_name' => $pipe
                ->wallet_name()
                ->isrequired()
                ->max(20)
                ->min(2)
                ->wallet_name,
            'wallet_id' => $pipe
                ->wallet_id()
                ->isrequired()
                ->max(20)
                ->min(2)
                ->tolower()
                ->wallet_id,
            'wallet_decimal' => $pipe
                ->wallet_decimal()
                ->default(2)
                ->wallet_decimal,
            'wallet_symbol' => $pipe
                ->wallet_symbol()
                ->isrequired()
                ->max(5)
                ->min(1)
                ->wallet_symbol,
            'wallet_description' => $pipe
                ->wallet_description()
                ->default('')
                ->wallet_description,
            'status' => $pipe
                ->wallet_status()
                ->default('enabled')
                ->isenum('enabled', 'disabled')
                ->wallet_status,
        ]);
    }

    public function creditPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            '_id' => GenerateKey(),
            'wallet_id' => $pipe
                ->wallet_id()
                ->isrequired()
                ->tolower()
                ->wallet_id,
            'amount' => $pipe
                ->amount()
                ->isrequired()
                ->tofloat()
                ->amount,
            'meta_data' => $pipe->meta_data
        ]);
    }
}

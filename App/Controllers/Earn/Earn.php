<?php
namespace App\Controllers\Earn;

use App\Controllers\Finance\Wallets\Wallets;
use Core\Pipes\Pipes;
use Core\Http\Res;

class Earn extends EarnControllers
{
    public function _earnFromTask(Pipes $p)
    {
        // $class = "TransactionMeta";
        // $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $class);
        // $class = strtolower($class) . 's';

        // Res::send($class);
        $pipe = $this->earnPipeValidation($p);
        $w = new Wallets();
        $w->creditWallet(new Pipes($pipe), $this->user);
    }

    public function _earnFromContest(Pipes $p)
    {
        
    }
}
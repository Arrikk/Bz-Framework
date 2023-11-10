<?php
namespace App\Controllers\Earn;

use Core\Pipes\Pipes;
use App\Controllers\Authenticated\Authenticated;

class EarnControllers extends Authenticated
{
    
    public function earnPipeValidation(Pipes $p)
    {
        return $p->pipe([
            'wallet_id' => 'zenar_point',
            'amount' => $p->amount()->isrequired()->toint()->amount,
            'reference' => '',
            'transaction_tag' => 'daily_task',
            'session_id' => $p->session_id()->isrequired()->session_id,
            'meta_data' => (object)[
                'transaction_request_id' => $p->session_id,
                'description' => "Earned ".$p->amount." point",
                'ref' => "task".time()
            ]
        ]);
    }
}
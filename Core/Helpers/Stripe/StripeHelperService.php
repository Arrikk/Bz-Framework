<?php

namespace Core\Helpers\Stripe;

use Core\Env;
use Core\Http\Res;

class StripeHelperService
{
    protected $stripe;
    
    public function __construct()
    {
        try {
            //code...
            $this->stripe = 
            new \Stripe\StripeClient(Env::STRIPE_SECRET_KEY());
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)::error(['message' => $th->getMessage()]);
        }
    }
}

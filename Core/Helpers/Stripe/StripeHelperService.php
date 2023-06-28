<?php

namespace Core\Helpers\Stripe;

use Core\Env;

class StripeHelperService
{
    protected $stripe;
    
    public function __construct()
    {
        $this->stripe = 
        new \Stripe\StripeClient(Env::STRIPE_SECRET_KEY());
    }
}

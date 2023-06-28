<?php

namespace App\Controllers\Stripe;

use Core\Controller;
use Core\Env;

class StripeService extends Controller
{
    protected $stripe;
    
    function before()
    {
        $this->stripe = 
        new \Stripe\StripeClient(Env::STRIPE_KEY());
    }
}

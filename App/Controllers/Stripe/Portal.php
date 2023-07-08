<?php

namespace App\Controllers\Stripe;

use App\Models\Subscription;
use Core\Controller;
use Core\Env;
use Core\Http\Res;

class Portal extends Controller
{
    public function portal($pipe)
    {
        $customer = $pipe->pipe([
            'id' => $pipe->customer_id()
                ->isrequired()
                ->false(Subscription::findOne([
                    'stripe_customer_id' => $pipe->customer_id
                ]), 'Customer not found')
                ->customer_id
        ]);
        try {
            //code...
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys
            \Stripe\Stripe::setApiKey(Env::STRIPE_KEY());
    
            // This is the URL to which the user will be redirected after they have
            // finished managing their billing in the portal.
            $return_url = Env::RETURN_URL();
            $stripe_customer_id = $customer->id;
    
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $stripe_customer_id,
                'return_url' => $return_url.'settings/billing',
            ]);
    
            Res::send($session);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error(['message' => $th->getMessage()]);
        }

        // Redirect to the URL for the session
        //   header("HTTP/1.1 303 See Other");
        //   header("Location: " . $session->url);
    }
}

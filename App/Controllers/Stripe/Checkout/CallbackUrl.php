<?php

namespace App\Controllers\Stripe\Checkout;

use App\Controllers\Stripe\StripeService;
use App\Models\Plan;
use App\Models\Subscription;
use App\Token;
use Core\Http\Res;
use Core\View;
use Error;

class CallbackUrl extends StripeService
{
    public function _success($data)
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($data->session_id);
            $customer = $this->stripe->customers->retrieve($session->customer);
            $decoded = Token::decodeJSON($data->papify);
            // Res::send([
            //     'papify' => $decoded,
            //     'customer' => $customer,
            //     'session' => $session
            // ]);
            $this->subscription($decoded, $customer, $session);


            return View::page('stripe/success.html');
        } catch (Error $e) {
            Res::status(500)::error($e->getMessage());
        }
    }

    public function _cancel()
    {
        return View::page('stripe/cancel.html');
    }

    function subscription($decoded, $customer, $session)
    {
        if (!Subscription::findOne(['user_id' => $decoded->user_id, 'and.stripe_subscription_id' => $session->subscription]))
            return Subscription::dump([
                'user_id' => $decoded->user_id,
                'plan_id' => $decoded->plan_id,
                'subscription_on' => $session->created,
                'subscription_expiry' => strtotime(($decoded->plan_duration ?? '+1Month')),
                'stripe_customer_id' => $customer->id,
                'stripe_session_id' => $session->id,
                'stripe_subscription_id' => $session->subscription,
                'stripe_session_data' => json_encode($session)
            ]);
    }
}

<?php

namespace App\Controllers\Stripe\Checkout;

use App\Controllers\Stripe\StripeService;
use App\Models\Plan;
use App\Models\Referral;
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
            $decoded = Token::decodeJSON($data->token);
            $this->subscription($decoded, $customer, $session);

            View::page("stripe/success.html", ['url' => $decoded->redirect ?? null]);
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
        if ($Subscription = Subscription::findOne(['user_id' => $decoded->user_id, 'and.stripe_subscription_id' => $session->subscription])){
           return $Subscription->modify([
                'user_id' => $decoded->user_id,
                'plan_id' => $decoded->plan_id,
                'updated_at' => date('Y-m-d H:i:s'),
           ]);
        }else {
            $Subscription = Subscription::dump([
                'user_id' => $decoded->user_id,
                'plan_id' => $decoded->plan_id,
                'status' => $session->status ?? PENDING,
                'subscription_on' => $session->created,
                'subscription_expiry' => strtotime(($decoded->plan_duration ?? '+1Month')),
                'stripe_customer_id' => $customer->id,
                'stripe_session_id' => $session->id,
                'stripe_subscription_id' => $session->subscription,
                'stripe_session_data' => json_encode($session)
            ]);
            $this->isRefferedUser($decoded->user_id, $decoded->plan_name, $decoded->transaction_amount ?? 0);
            return $Subscription;
        }
    }
    
    public function isRefferedUser($userID, $plan, $amount) {
        if(!$userID) return false;
        $ref = Referral::findOne(['referred_id' => $userID]);
        if(!$ref) return false;
        $ref->modify([
            'plan' => $plan,
            'status' => CONVERTED,
            'commission' => $amount === 0 ? $amount * 0.01 : $amount
        ]);
    }
}
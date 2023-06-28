<?php
namespace App\Controllers\Stripe\Checkout;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\Stripe\StripeService;
use App\Models\Finance\Transaction;
use App\Models\Plan;
use App\Token;
use Core\Env;
use Core\Http\Res;

class CheckoutSession extends Authenticated
{
    public function _checkout($data)
    {
        $data = $data->pipe(['price' => $data->price_id()->isrequired()->price_id]);
        $checkoutData = $this->checkoutIn($data->price, $this->user->id);

        \Stripe\Stripe::setApiKey(Env::STRIPE_KEY());
        $session = \Stripe\Checkout\Session::create([
            'success_url' => Env::BASE_URI().'stripe/success?session_id={CHECKOUT_SESSION_ID}&papify='.$checkoutData['token'],
            'cancel_url' => Env::BASE_URI().'stripe/canceled',
            'mode' => 'subscription',
            'line_items' => [[
              'price' => $data->price,
              // For metered billing, do not pass quantity
              'quantity' => 1,
            ]],
          ]);

          Res::send([
            'session_id' => $session->id,
            'url' => $session->url
          ]);
    }


    public function checkoutIn($plan_price_id, $userID)
    {
        // SubscriptionService::hasActiveSubscriptionService($userID);

        $plan = Plan::findOne(['amount_id' => $plan_price_id]);
        if (!$plan) Res::status(400)::error("Invalid Subscription Plan");

        $tx = Transaction::dump([
            'user_id' => $userID,
            'transaction_amount' => $plan->plan_amount,
            'transaction_reference' => time() . 'TXUSR' . $userID,
        ]);

        return [
            'token' => Token::encodeJSON(
                [
                    'user_id' => $userID,
                    'plan_id' => $plan->plan_id,
                    'plan_duration' => $plan->plan_duration,
                    'amount_id' => $plan_price_id,
                    'transaction_amount' => $plan->plan_amount,
                    'transaction_id' => $tx->id,
                    'transaction_reference' => $tx->transaction_reference
                ],
            ),
            'plan_amount' => $plan->plan_amount
        ];
    }
}
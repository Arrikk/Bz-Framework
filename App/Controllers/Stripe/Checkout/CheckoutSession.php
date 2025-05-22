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
        // $data = $data->pipe(['price' => $data->price_id()->isrequired()->price_id]);
        $checkoutData = $this->checkoutIn($data->plan_id, $this->user->id);
        try {
            //code...
            \Stripe\Stripe::setApiKey(Env::STRIPE_SECRET_KEY());
            // $base = Env::BASE_URI();
            $url = formatUrl(Env::BASE_URI());
            $stripePayload = [
                'success_url' => $url . '/payment/success?session_id={CHECKOUT_SESSION_ID}&token=' . $checkoutData['token'],
                'cancel_url' => $url . '/payment/cancel',
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $checkoutData['amount_id'],
                    // For metered billing, do not pass quantity
                    'quantity' => 1,
                ]],
            ];
            if($data->customer): 
                $stripePayload['customer'] = $data->customer;
            else: 
                $stripePayload['subscription_data'] = [
                    'trial_period_days' => 1,
                ];
                $stripePayload['customer_email'] = $this->user->email;
            endif;
            $session = \Stripe\Checkout\Session::create($stripePayload);

            Res::send([
                'session_id' => $session->id,
                'url' => $session->url
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)::error(['message' => $th->getMessage()]);
        }
    }


    public function checkoutIn($plan_id, $userID)
    {
        // SubscriptionService::hasActiveSubscriptionService($userID);

        $plan = Plan::findOne(['plan_id' => $plan_id]);
        if (!$plan) Res::status(400)::error("Invalid Subscription Plan");

        // Res::status(400)::send($plan);

        // $tx = Transaction::dump([
        //     'user_id' => $userID,
        //     'transaction_amount' => $plan->plan_amount,
        //     'transaction_reference' => time() . 'TXUSR' . $userID,
        // ]);

        return [
            'token' => Token::encodeJSON(
                [
                    'user_id' => $userID,
                    'plan_id' => $plan->plan_id,
                    "wallet_id" => "USD",
                    'plan_duration' => $plan->plan_duration,
                    'amount_id' => $plan->amount_id,
                    'transaction_amount' => $plan->plan_amount,
                    // 'transaction_id' => $tx->id,
                    'plan_name' => $plan->plan_name,
                    // 'transaction_reference' => $tx->transaction_reference
                ],
            ),
            'amount_id' => $plan->amount_id,
            'plan_amount' => $plan->plan_amount
        ];
    }
}

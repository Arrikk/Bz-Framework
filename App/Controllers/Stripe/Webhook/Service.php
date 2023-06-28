<?php

namespace App\Controllers\Stripe\Webhook;

use App\Models\Subscription;
use Core\Http\Res;

class Service
{
    public static function updateCustomerSubscription($customer, $data, $status = true)
    {
        # When the subscription starts
        $currentPeriodStarts = ($data->current_period_starts ?? ($data->created ?? time()));
        # when subscription should end
        $currentPeriodEnds = $data->cancel_at_period_end ? ($data->canceled_at ?? time() - 5000) : ($data->current_period_end ?? (time() + 60 - 60));
        # customer ID
        $customer = ($data->customer ?? null);
        # get the current plan user is subscribed to
        # get the plan price id
        $price = ($data->plan->price ?? null);
        #get the plan product id
        $product = ($data->plan->product ?? null);

        // Res::json([
        //     'start' => date('Y-m-d H:i:s', $currentPeriodStarts),
        //     'end' => date('Y-m-d H:i:s', $currentPeriodEnds),
        //     'customer' => $customer,
        //     'product' => $product,
        // ]);
        $updateSubscription = Subscription::findAndUpdate(['stripe_customer_id' => $customer, 'and.plan_id' => $product], [
            'subscription_on' => $currentPeriodStarts,
            'subscription_expiry' => $currentPeriodEnds,
            // 'stripe_session_id' => $session->id,
            'stripe_subscription_id' => $data->id,
            'stripe_session_data' => json_encode($data)
        ]);

        if ($updateSubscription) Res::status(201)::send([
            'message' => "Subscription Updated",
            "subscription" => $updateSubscription->append([
                'subscribed_on' => date('Y-m-d H:i:s a', (int) $updateSubscription->subscription_on),
                'expiry_on' => date('Y-m-d H:i:s a', (int) $updateSubscription->subscription_expiry),
            ])->remove('stripe_session_data')
        ]);
        Res::status(200)::send([
            "message" => "Subscription updated but failed from third party app",
            'start' => date('Y-m-d H:i:s', $currentPeriodStarts),
            'end' => date('Y-m-d H:i:s', $currentPeriodEnds),
            'customer' => $customer,
            'product' => $product,
        ]);
    }


    public static function sendInvoice($invoice, $customerEmail, $customerID)
    {
    }
}

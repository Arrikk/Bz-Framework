<?php

namespace App\Controllers\Stripe\Webhook;

use App\Email\Emails;
use App\Models\Subscription;
use Core\Http\Res;

class Service
{
    public static function createCustomerSubscription($data)
    {
        $item = $data->items->data[0];
        # When the subscription starts
        $currentPeriodStarts = ($item->current_period_starts ?? ($data->created ?? time()));
        # when subscription should end
        $currentPeriodEnds = ($item->current_period_end ?? $data->trial_end ?? (time() + 60 - 60));
        # customer ID
        $customer = ($data->customer ?? null);
        # get the current plan user is subscribed to
        #get the plan product id
        $product = ($item->plan->product ?? null);
        $id = $item->subscription ?? null;
        $status = $data->status;

        self::completeSubscription(
            customer: $customer,
            status: $status,
            subscription: $id,
            product: $product,
            data: $item,
            currentPeriodStarts: $currentPeriodStarts,
            currentPeriodEnds: $currentPeriodEnds,
        );

    }
    public static function subscriptionSessionCompleted($data)
    {
        # customer ID
        $customer = ($data->customer ?? null);
        $id = $item->subscription ?? null;
        $status = $data->status == "complete" ? "active" : "pending";

        self::completeSubscription(
            customer: $customer,
            status: $status,
            subscription: $id
        );

    }
    public static function updateSubscriptionStatus($data)
    {
        # customer ID
        $customer = ($data->customer ?? null);
        $id = $item->subscription ?? null;
        $status = (isset($data->cancel_at_period_end) && $data->cancel_at_period_end) ? 'to_be_canceled' : $data->status;

        self::completeSubscription(
            customer: $customer,
            status: $status,
            subscription: $id
        );

    }
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

    private static function completeSubscription($customer, $status, $subscription = null, $product = null, $data = null, $currentPeriodStarts = null, $currentPeriodEnds = null) {
        $payload = [
            'stripe_customer_id' => $customer,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if($currentPeriodEnds) $payload['subscription_expiry'] = $currentPeriodEnds;
        if($currentPeriodStarts) $payload['subscription_on'] = $currentPeriodStarts;
        if($currentPeriodEnds) $payload['subscription_expiry'] = $currentPeriodEnds;
        if($subscription) $payload['stripe_subscription_id'] = $subscription;
        if($data) {
            $payload['stripe_session_data'] = json_encode($data);
            $payload['stripe_session_id'] = $data->id ?? null;  
        }

        $updateSubscription = Subscription::findOne(['stripe_customer_id' => $customer, 'and.plan_id' => $product]);

        if ($updateSubscription):
            $updateSubscription->modify($payload);
            Res::status(200)::send([
                'message' => "Subscription Updated",
                "subscription" => $updateSubscription->append([
                    'subscribed_on' => date('Y-m-d H:i:s a', (int) $updateSubscription->subscription_on),
                    'expiry_on' => date('Y-m-d H:i:s a', (int) $updateSubscription->subscription_expiry),
                ])->remove('stripe_session_data')
            ]);
        else:
            $payload['user_id'] = $customer;
            $createSub = Subscription::dump($payload);
            Res::status(201)::send([
                'message' => "Subscription Created",
                "subscription" => $createSub->append([
                    'subscribed_on' => date('Y-m-d H:i:s a', (int) $createSub->subscription_on),
                    'expiry_on' => date('Y-m-d H:i:s a', (int) $createSub->subscription_expiry),
                ])->remove('stripe_session_data')
            ]);
        endif;
        Res::status(200)::send([
            "message" => "Subscription updated but failed from third party app",
            'start' => date('Y-m-d H:i:s', $currentPeriodStarts),
            'end' => date('Y-m-d H:i:s', $currentPeriodEnds),
            'customer' => $customer,
            'product' => $product,
        ]);
    }

    public static function sendInvoice($data)
    {

        $email = $data->customer_email;
        $name = $data->customer_name;
        $invoice = $data->hosted_invoice_url;

        Emails::sendInvoice(
            email: $email,
            name: $name,
            invoice: $invoice
        );
    }
}
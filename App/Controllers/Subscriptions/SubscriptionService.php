<?php

namespace App\Controllers\Subscriptions;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\Plans\PlanService;
use App\Models\Plan;
use App\Models\Subscription;
use Core\Controller;
use Core\Http\Res;
use Core\Pipes\Pipes;

class SubscriptionService extends Authenticated
{
    /**
     * Create a new plan Service...
     * Verify data, serialize and validate data...
     */
    public function createSubscriptionService(Pipes $pipe)
    {
        return $pipe->pipe([
            "plan_id" => $pipe->plan_id()->isrequired()->plan_id,
            "amount_id" => $pipe->amount_id()->isrequired()->amount_id
        ]);
    }

    /**
     * Update a new plan Service...
     * Verify data, serialize and validate data...
     */
    public function updateSubscriptionService(Pipes $pipe, $planID)
    {
        $plan = Plan::findById($planID);
        if (!$plan) Res::json($plan);
        return $pipe->pipe([
            "plan_name" => $pipe->name()->default($plan->plan_name)->capitalize()->name,
            "plan_desc" => $pipe->description()->default($plan->plan_desc ?? '')->serialize()->description,
            "plan_duration" => $pipe->duration()->default($plan->plan_duration ?? '')->serialize()->duration,
            "plan_features" => $pipe->features()->default($plan->plan_features ?? '')->isstring()->serialize()->features
        ]);
    }

    function formatSubscriptionService($subscription) {
        if(!$subscription instanceof Subscription) return $subscription;

        return $subscription->append([
            'active' => (int) $subscription->subscription_expiry >= time(),
            'expired' => (int) $subscription->subscription_expiry < time(),
            'subscription_on_date' => date( 'D M Y H:i A', $subscription->subscription_on),
            'subscription_off_date' => date( 'D M Y H:i A', $subscription->subscription_expiry),
            'plan' => PlanService::formatPlanService(Plan::findOne(['plan_id' => $subscription->plan_id])),
            'subscription_data' => json_decode($subscription->stripe_session_data),
        ])->remove('stripe_session_data', 'plan_id', 'updated_at', 'created_at');
    }

    public static function hasActiveSubscriptionService($userID)
    {
        $mySubscription = Subscription::findOne(['user_id' => $userID]);
        if(!$mySubscription) return;

        $expired = time() >= (int) $mySubscription->subscription_expiry;
        if($expired) return;

        Res::status(400)::error([
            "message" => "You have an active subscription", 
            "subscription" => $mySubscription->only('subscription_on', 'subscription_expiry')
        ]);
    }
}

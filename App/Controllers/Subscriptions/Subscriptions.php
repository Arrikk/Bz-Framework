<?php

namespace App\Controllers\Subscriptions;

use App\Controllers\License;
use App\Controllers\Stripe\Checkout\CheckoutSession;
use App\Helpers\Filters;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Core\Http\Res;
use Core\Model\Model;
use Core\Pipes\Pipes;

class Subscriptions extends SubscriptionService
{
    /**
     * Create Syb Controller
     * @var Pipes $pipe... Post data
     */
    public function _subscribe(Pipes $pipe)
    {
        try {
            Model::beginTransaction();
            $create = $this->createSubscriptionService($pipe);
    
            // Res::send($create->plan_id);
            $active = SubscriptionService::hasActiveSubscriptionService($this->authenticated->id);
            $checkout = (new CheckoutSession)->checkout(new Pipes([
                'plan_id' => $create->plan_id,
                'customer' => $active
            ]));
            Model::commitTransaction();
            Res::send($checkout);
        } catch (\Throwable $th) {
            Model::rollBackTransaction();
        }


        // return Subscription::dump((array) $create);
    }

    /**
     * Update Plan Controller
     * @var Pipes $pipe
     * Update a pplan by their ID
     */
    public function _updateSubscription(Pipes $pipe)
    {
        $subscriptionID = $this->route_params['id'];
        $planPipe = $this->updateSubscriptionervice($pipe, $subscriptionID);
        $update = Subscription::findAndUpdate(['id' => $subscriptionID], (array) $planPipe);
        Res::json($update);
    }

    /**
     * Delete Subscription Controller
     * Delete a Subscription by its ID
     */
    public function _deleteSubscription(Pipes $pipe)
    {
        $subscriptionID = $this->route_params['id'];
        $del = Subscription::findAndDelete(['id' => $subscriptionID]);
        Res::json(["message" => "Deleted", "deleted" => $del]);
    }

    /**
     * Get Subscription Controller
     * Get a Subscription by its ID
     */
    public function _getSubscriptionById()
    {
        $subscriptionID = $this->route_params['id'];
        $subscription = Subscription::findById($subscriptionID);
        Res::json($subscription);
    }

    /**
     * Get Subscription Controller
     * Get all Subscription
     */
    public function _getSubscription()
    {
        $subscriptions = Plan::find();
        $filters = Filters::from($subscriptions)->append([
            'plan_features' => function ($feature) {
                $features = Feature::find(['$.where' => Feature::in('id', $feature)]);
                return Filters::from($features)->only('feature_name', 'feature_key')->done();
            }

        ])->done();
        Res::json($filters);
    }

    public function _subscription($userID)
    {
        $user = isset($this->auth) ? $this->authenticated->id : $userID;
        $mysub = Subscription::findOne(['user_id' => $user]);
        // if(!$mysub) Res::status(404)->error("You do not have ");
        Res::send($this->formatSubscriptionService($mysub));
        // return $mysub;
    }

    public function _subscriptions()
    {
        $subscriptions = Subscription::find();
        $filters = Filters::from($subscriptions)->append([
            'subscription_expiry.active' => fn ($expiry) => (int) $expiry >= time(),
            'subscription_expiry.expired' => fn ($expiry) => (int) $expiry < time(),
            'subscription_expiry.expiry_date' => fn ($expiry) => date('D M Y H:i:s', $expiry),
            'subscription_on.subscription_date' => fn ($subscriptionDate) => date('D M Y H:i:s', $subscriptionDate),
            'user_id.user' => fn ($user) => User::findById($user, '', 'first_name, last_name, email, address, country'),
            'plan_id.plan' => fn ($plan) => Plan::findOne(['plan_id' => $plan], 'plan_id, plan_name, plan_desc, plan_amount, plan_duration'),
        ])->done();

        Res::send($filters);
    }
}
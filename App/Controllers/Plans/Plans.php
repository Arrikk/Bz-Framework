<?php

namespace App\Controllers\Plans;

use App\Models\Plan;
use Core\Helpers\Stripe\Products\Prices;
use Core\Helpers\Stripe\Products\Products;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Plans extends PlanService
{
    /**
     * Create Plan Controller
     * @var Pipes $pipe... Post data
     */
    public function _createPlan(Pipes $pipe)
    {
        $create = $this->createPlanService($pipe);
         
        # create a stripe product for this subscription Plan...
        # pipe products data in to product create method.
        $stripeProduct = (new Products())->create(new Pipes([
            'name' => $create->plan_name,
            'description' => $create->plan_desc
        ]));
        if(!$stripeProduct) Res::status(400)->error("Error creating product");

        # create a price for created product... 
        $stripePrice = (new Prices())->setPrice(new Pipes([
            'amount' => $create->plan_amount,
            'product' => $stripeProduct->id
        ]));

        $createPlanLocal = Plan::dump(array_merge((array) $create, [
            'amount_id' => $stripePrice->id,
            'plan_id' => $stripeProduct->id
        ]));

        Res::json($this->formatPlanService($createPlanLocal));
    }

    /**
     * Update Plan Controller
     * @var Pipes $pipe
     * Update a pplan by their ID
     */
    public function _updatePlan(Pipes $pipe)
    {
        $planID = $this->route_params['id'];
        $planPipe = $this->updatePlanService($pipe, $planID);
        $update = Plan::findAndUpdate(['id' => $planID], (array) $planPipe);
        Res::json($update);
    }

    /**
     * Delete Plan Controller
     * Delete a pplan by its ID
     */
    public function _deletePlan(Pipes $pipe)
    {
        $planID = $this->route_params['id'];
        $del = Plan::findAndDelete(['id' => $planID]);
        Res::json(["message" => "Deleted", "deleted" => $del]);
    }

    /**
     * Get Plan Controller
     * Get a plan by its ID
     */
    public function _getPlanById()
    {
        $plainId = $this->route_params['id'];
        $plan = Plan::findById($plainId);
        Res::json($plan);
    }

    /**
     * Get Plans Controller
     * Get all Plans
     */
    public function _getPlan()
    {
        $plans = $this->formatPlansService(Plan::find());
        Res::json($plans);
    }
}

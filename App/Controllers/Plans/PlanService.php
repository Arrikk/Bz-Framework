<?php
namespace App\Controllers\Plans;

use App\Helpers\Filters;
use App\Models\Feature;
use App\Models\Plan;
use Core\Controller;
use Core\Http\Res;
use Core\Pipes\Pipes;

class PlanService extends Controller
{
    /**
     * Create a new plan Service...
     * Verify data, serialize and validate data...
     */
    public function createPlanService(Pipes $pipe)
    {
       return $pipe->pipe([
        "plan_amount" => $pipe->price()->isrequired()->match('/\d+/')->price,
        "plan_name" => $pipe->name()->isrequired()->capitalize()->name,
        "plan_desc" => $pipe->description()->isrequired()->serialize()->description,
        "plan_duration" => $pipe->duration()->isrequired()->serialize()->duration,
        "plan_features" => $pipe->features()->isrequired()->isstring()->serialize()->features
       ]);
    }

    /**
     * Update a new plan Service...
     * Verify data, serialize and validate data...
     */
    public function updatePlanService(Pipes $pipe, $planID)
    {
        $plan = Plan::findById($planID);
        if(!$plan) Res::json($plan); 
       return $pipe->pipe([
        "plan_name" => $pipe->name()->default($plan->plan_name)->capitalize()->name,
        "plan_desc" => $pipe->description()->default($plan->plan_desc ?? '')->serialize()->description,
        "plan_duration" => $pipe->duration()->default($plan->plan_duration ?? '')->serialize()->duration,
        "plan_features" => $pipe->features()->default($plan->plan_features ?? '')->isstring()->serialize()->features
       ]);
    }

    public static function formatPlanService($plan)
    {
        if(!$plan instanceof Plan) return $plan;
        return $plan->append([
            'duration' => strtotime($plan->plan_duration),
            'plan_features' => Feature::find(['$.where' => Feature::in('id', $plan->plan_features)], 'feature_name, feature_key, feature_value')
        ]);
    }

    public function formatPlansService($plans)
    {
        return Filters::from($plans)->append([
            'plan_duration.duration' => fn($duration) => strtotime($duration),
            'plan_features' => fn($features) => Feature::find(['$.where' => Feature::in('id', $features)], 'feature_name, feature_key, feature_value')
        ])->done();
    }
}
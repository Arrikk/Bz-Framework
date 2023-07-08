<?php

namespace App\Controllers\Features;

use App\Models\Feature;
use App\Models\Plan;
use Core\Controller;
use Core\Http\Res;
use Core\Pipes\Pipes;

class FeatureService extends Controller
{
    private $featureKey = [
        'interactive_investigative_dashboard',
        'seamless_platform_integration',
        'integrated_big_data_analytics',
        'realtime_behavioral_analytics',
        'dynamic_risk_scoring',
        'advanced_artificial_intelligence',
        'predictive_insights_and_alert_prioritization',
        'continuous_learning_and_model_optimization',
        'regulatory_compliance_automation'
    ];
    /**
     * Create a new Feature Service...
     * Verify data, serialize and validate data...
     */
    public function createFeatureService(Pipes $pipe)
    {
        return $pipe->pipe([
            "feature_name" => $pipe->name()->isrequired()->capitalize()->name,
            "feature_key" => $pipe->key()->isrequired()->isenum(...$this->featureKey)->key,
            "feature_value" => $pipe->value()->isrequired()->value
        ]);
    }

    /**
     * Update a new plan Service...
     * Verify data, serialize and validate data...
     */
    public function updateFeatureService(Pipes $pipe, $featureID)
    {
        $feature = Feature::findById($featureID);
        if (!$feature) Res::json($feature);
        return $pipe->pipe([
            "feature_value" => $pipe->value()->default($feature->feature_value)->serialize()->value,
            "feature_name" => $pipe->name()->default($feature->feature_name)->capitalize()->name
        ]);
    }
}

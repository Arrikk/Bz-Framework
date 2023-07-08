<?php

namespace App\Controllers\Features;

use App\Models\Feature;
use App\Models\Plan;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Features extends FeatureService
{
    /**
     * Create Feature Controller
     * @var Pipes $pipe... Post data
     */
    public function _createFeature(Pipes $pipe)
    {
        $create = $this->createFeatureService($pipe);
        Res::json(Feature::dump((array) $create));
    }

    /**
     * Update Feature Controller
     * @var Pipes $pipe
     * Update a pFeature by their ID
     */
    public function _updateFeature(Pipes $pipe)
    {
        $featureID = $this->route_params['id'];
        $featurePipe = $this->updateFeatureService($pipe, $featureID);
        $update = Feature::findAndUpdate(['id' => $featureID], (array) $featurePipe);
        Res::json($update);
    }

    /**
     * Delete Feature Controller
     * Delete a Feature by its ID
     */
    public function _deleteFeature(Pipes $pipe)
    {
       $featureID = $this->route_params['id'];
       $del= Feature::findAndDelete(['id' => $featureID]);
       Plan::findAndDelete([
        '$.where' => Plan::inset($featureID, 'plan_features')
       ]);
       Res::json(["message" => "Deleted", "deleted" => $del]);
    }

    /**
     * Get Feature Controller
     * Get a Feature by its ID
     */
    public function _getFeatureById()
    {
        $featureID = $this->route_params['id'];
        $feature = Feature::findById($featureID);
        Res::json($feature);
    }

    /**
     * Get Feature Controller
     * Get all Feature
     */
    public function _getFeature()
    {
    $features = Feature::find();
    Res::json($features);
    }
}

<?php

namespace App\Controllers\Stripe\Products;

use App\Controllers\Stripe\StripeService;
use Core\Http\Res;

class Prices extends StripeService
{
    public function _setPrice($data)
    {
        $data = $data->pipe([
            'amount' => $data->amount()->isrequired()->isint()->toint()->amount,
            'product' => $data->product()->isrequired()->isstring()->product
        ]);

        $price = $this->stripe->prices->create([
            'unit_amount' => $data->amount,
            'currency' => 'usd',
            'recurring' => ['interval' => 'month'],
            'product' => $data->product,
        ]);

        Res::send($price);
    }

    public function _getPrices(){
        $prices = $this->stripe->prices->all(['limit' => 3]);
        Res::send($prices);
    }
}

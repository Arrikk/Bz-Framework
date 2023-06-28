<?php

namespace Core\Helpers\Stripe\Products;

use Core\Helpers\Stripe\StripeHelperService;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Prices extends StripeHelperService
{
    public function setPrice(Pipes $data)
    {
        $data = $data->pipe([
            'amount' => $data->amount()->isrequired()->isint()->toint()->amount,
            'product' => $data->product()->isrequired()->isstring()->product
        ]);

        $price = $this->stripe->prices->create([
            'unit_amount' => ($data->amount * 100),
            'currency' => 'usd',
            'recurring' => ['interval' => 'month'],
            'product' => $data->product,
        ]);

        return $price;
        Res::send($price);
    }

    public function getPrices(){
        $prices = $this->stripe->prices->all(['limit' => 3]);
        Res::send($prices);
    }
}

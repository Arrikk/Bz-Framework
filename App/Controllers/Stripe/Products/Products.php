<?php

namespace App\Controllers\Stripe\Products;

use App\Controllers\Stripe\StripeService;
use Core\Http\Res;

class Products extends StripeService
{

    public function _create($data)
    {
        $data->pipe([
            'name' => $data->name()->isrequired()->name,
            'description' => $data->description()->isrequired()->description,
            'data' => $data->data
        ]);

        $product = $this->stripe->products->create([
            'name' => $data->name,
            'description' => $data->description,
            'metadata' => (array) ($data->data)
        ]);

        Res::send($product);
    }

    public function _getProducts()
    {
        $products = $this->stripe->products->all(['limit' => 3]);
        Res::send($products);
    }
}

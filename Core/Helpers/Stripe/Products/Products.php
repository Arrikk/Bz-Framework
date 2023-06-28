<?php

namespace Core\Helpers\Stripe\Products;

use Core\Helpers\Stripe\StripeHelperService;
use Core\Http\Res;

class Products extends StripeHelperService
{

    public function create($data)
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

        return $product;
        Res::send($product);
    }

    public function getProducts()
    {
        $products = $this->stripe->products->all(['limit' => 3]);
        Res::send($products);
    }
}

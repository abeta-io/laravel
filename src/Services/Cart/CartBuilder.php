<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Services\Cart;

use AbetaIO\Laravel\Models\Product;
use InvalidArgumentException;

class CartBuilder
{
    protected $cartGeneral = [];

    protected $products = [];

    /**
     * Set general cart information.
     *
     * @return $this
     */
    public function setGeneral(array $general): self
    {
        $requiredFields = ['cart_id', 'total', 'currency', 'delivery_datetime'];

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $general)) {
                throw new InvalidArgumentException("The {$field} field is required in general information.");
            }
        }

        $this->cartGeneral = $general;

        return $this;
    }

    /**
     * Add a product to the cart.
     *
     * @return $this
     */
    public function addProduct(Product $product): self
    {
        $this->products[] = $product->toArray();

        return $this;
    }

    /**
     * Build and return the cart data.
     */
    public function build(): array
    {
        if (empty($this->cartGeneral)) {
            throw new InvalidArgumentException('General cart information is required.');
        }

        return [
            'general' => $this->cartGeneral,
            'products' => $this->products,
        ];
    }
}

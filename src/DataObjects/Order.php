<?php
declare(strict_types=1);

namespace AbetaIO\Laravel\DataObjects;

use Illuminate\Support\Collection;

class Order
{
    public $cart_id;
    public $name;
    public $total;
    public $currency;
    public $delivery_datetime;
    public $order_reference;
    public $customer_reference;
    public Collection $products;
    public Address $billTo;
    public Address $shippTo;

    public function __construct(
        $cart_id, 
        $total, 
        $currency, 
        $delivery_datetime,
        $order_reference,
        $customer_reference,
        $products,
        $billTo,
        $shippTo,
    ) {
        $this->cart_id = $cart_id;
        $this->total = $total;
        $this->currency = $currency;
        $this->delivery_datetime = $delivery_datetime;
        $this->order_reference = $order_reference;
        $this->customer_reference = $customer_reference;
        $this->products = $products;
        $this->billTo = $billTo;
        $this->shippTo = $shippTo;
    }

    /**
     * Convert the Order to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'cart_id' => $this->cart_id,
            'total' => $this->total,
            'currency' => $this->currency,
            'delivery_datetime' => $this->delivery_datetime,
            'order_reference' => $this->order_reference,
            'customer_reference' => $this->customer_reference,
            'products' => $this->products->map(function($product) { return $product->toArray(); })->toArray(),
            'bill_to' => $this->billTo->toArray(),
            'shipp_to' => $this->shippTo->toArray(),
        ];
    }
}
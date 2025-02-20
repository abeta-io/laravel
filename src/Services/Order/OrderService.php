<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Services\Order;

use AbetaIO\Laravel\DataObjects\Address;
use AbetaIO\Laravel\DataObjects\Order;
use AbetaIO\Laravel\Events\OrderReceived;
use AbetaIO\Laravel\Models\Product;

class OrderService
{
    protected static $callbacks = [];

    /**
     * Process the order.
     *
     * @throws \Exception
     */
    public function processOrder(array $orderData): Order
    {
        $this->validateOrder($orderData);

        $standardizedOrder = $this->standardizeOrder($orderData);

        event(new OrderReceived($standardizedOrder));

        foreach (self::$callbacks as $callback) {
            call_user_func($callback, $standardizedOrder);
        }

        return $standardizedOrder;
    }

    /**
     * Validate the order data.
     *
     * @throws \Exception
     */
    private function validateOrder(array $orderData): void
    {
        if (empty($orderData['cart_id']) || empty($orderData['customer_reference'])) {
            throw new \Exception('Invalid order data: Missing required fields.');
        }
    }

    /**
     * Standardize the order structure.
     */
    private function standardizeOrder(array $orderData): Order
    {
        $billTo = Address::fromArray($orderData['billing'] ?? []);
        $shippTo = Address::fromArray($orderData['shipping'] ?? []);

        $products = collect($orderData['products'])->map(function ($product) {
            return Product::fromArray($product);
        });

        return new Order(
            $orderData['cart_id'] ?? null,
            $orderData['total'] ?? 0.0,
            $orderData['currency'] ?? 'EUR',
            $orderData['delivery_datetime'] ?? null,
            $orderData['order_reference'] ?? null,
            $orderData['customer_reference'] ?? null,
            $products,
            $billTo,
            $shippTo
        );
    }

    /**
     * Register a callback for additional processing.
     */
    public static function onOrderProcessed(callable $callback): void
    {
        self::$callbacks[] = $callback;
    }
}

<?php

namespace AbetaIO\Laravel\Services\Order;

use AbetaIO\Laravel\DataObjects\Address;
use AbetaIO\Laravel\DataObjects\Order;
use AbetaIO\Laravel\Events\OrderReceived;
use AbetaIO\Laravel\Models\Product;
use InvalidArgumentException;

class OrderService
{
    protected static $callbacks = [];

    /**
     * Process the order.
     *
     * @param array $orderData
     * @return bool
     * @throws \Exception
     */
    public function processOrder(array $orderData): Order
    {
        // Step 1: Validate the order
        $this->validateOrder($orderData);

        // Step 2: Standardize the order structure
        $standardizedOrder = $this->standardizeOrder($orderData);

        // Step 3: Trigger the OrderReceived event
        event(new OrderReceived($standardizedOrder));

        // Step 4: Invoke any registered callbacks
        foreach (self::$callbacks as $callback) {
            call_user_func($callback, $standardizedOrder);
        }

        return $standardizedOrder;
    }

    /**
     * Validate the order data.
     *
     * @param array $orderData
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
     *
     * @param array $orderData
     * @return Order
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
     *
     * @param callable $callback
     */
    public static function onOrderProcessed(callable $callback): void
    {
        self::$callbacks[] = $callback;
    }
}

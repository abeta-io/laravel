<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Events;

use AbetaIO\Laravel\DataObjects\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderReceived
{
    use Dispatchable, SerializesModels;

    /**
     * The standardized order data.
     *
     * @var Order
     */
    public $orderData;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $orderData)
    {
        $this->orderData = $orderData;
    }
}

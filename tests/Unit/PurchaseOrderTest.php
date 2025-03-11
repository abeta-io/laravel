<?php

declare(strict_types=1);

namespace Tests\Unit;

use AbetaIO\Laravel\Events\OrderReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    private string $api_key;

    /**
     * Test setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->api_key = env('ABETA_API_KEY');
    }

    /**
     * Test if the is_abeta_punchout_user() helper works as expected
     */
    public function test_order_confirmation_via_event_listener(): void
    {
        Event::fake();

        $response = $this->post(route('abeta.order.confirm'), $this->purchaseOrder());

        Event::assertDispatched(OrderReceived::class, function ($event) {
            $po = $this->purchaseOrder();

            return $event->orderData->cart_id == $po['cart_id'] &&
                    $event->orderData->total == $po['total'] &&
                    $event->orderData->currency == $po['currency'] &&
                    $event->orderData->delivery_datetime == $po['delivery_datetime'] &&
                    $event->orderData->order_reference == $po['order_reference'] &&
                    $event->orderData->customer_reference == $po['customer_reference'] &&

                    $event->orderData->billTo->first_name == $po['billing']['first_name'] &&
                    $event->orderData->billTo->last_name == $po['billing']['last_name'] &&
                    $event->orderData->billTo->company == $po['billing']['company'] &&
                    $event->orderData->billTo->address_1 == $po['billing']['address_1'] &&
                    $event->orderData->billTo->address_2 == $po['billing']['address_2'] &&
                    $event->orderData->billTo->street == $po['billing']['street'] &&
                    $event->orderData->billTo->city == $po['billing']['city'] &&
                    $event->orderData->billTo->state == $po['billing']['state'] &&
                    $event->orderData->billTo->postcode == $po['billing']['postcode'] &&
                    $event->orderData->billTo->country == $po['billing']['country'] &&
                    $event->orderData->billTo->email == $po['billing']['email'] &&
                    $event->orderData->billTo->phone == $po['billing']['phone'] &&

                    $event->orderData->shippTo->first_name == $po['shipping']['first_name'] &&
                    $event->orderData->shippTo->last_name == $po['shipping']['last_name'] &&
                    $event->orderData->shippTo->company == $po['shipping']['company'] &&
                    $event->orderData->shippTo->address_1 == $po['shipping']['address_1'] &&
                    $event->orderData->shippTo->address_2 == $po['shipping']['address_2'] &&
                    $event->orderData->shippTo->street == $po['shipping']['street'] &&
                    $event->orderData->shippTo->city == $po['shipping']['city'] &&
                    $event->orderData->shippTo->state == $po['shipping']['state'] &&
                    $event->orderData->shippTo->postcode == $po['shipping']['postcode'] &&
                    $event->orderData->shippTo->country == $po['shipping']['country'] &&
                    $event->orderData->shippTo->email == $po['shipping']['email'] &&
                    $event->orderData->shippTo->phone == $po['shipping']['phone'] &&

                    $event->orderData->remark == $po['remark']
                    
                    ;
        });
    }

    private function purchaseOrder(): array
    {
        return [
            'api_key' => $this->api_key,
            'cart_id' => '123',
            'total' => '100.00',
            'currency' => 'EUR',
            'delivery_datetime' => '2025-12-12 20:12:12',
            'order_reference' => 'I123456',
            'customer_reference' => 'customer@example.com',
            'billing' => [
                'first_name' => 'billing_first_name',
                'last_name' => 'billing_last_name',
                'company' => 'billing_company',
                'address_1' => 'billing_address_1',
                'address_2' => 'billing_address_2',
                'street' => 'street 123',
                'city' => 'city',
                'state' => 'state',
                'postcode' => '1234AB',
                'country' => 'NL',
                'email' => 'email@example.com',
                'phone' => '012345678',
            ],
            'shipping' => [
                'first_name' => 'shipping_first_name',
                'last_name' => 'shipping_last_name',
                'company' => 'shipping_company',
                'address_1' => 'shipping_address_1',
                'address_2' => 'shipping_address_2',
                'street' => null,
                'city' => 'city',
                'state' => 'state',
                'postcode' => '1234AB',
                'country' => 'NL',
                'email' => 'email@example.com',
                'phone' => '012345678',
            ],

            'products' => [[
                'id' => 101,
                'sku' => 'apple-123',
                'name' => 'Apple test product',
                'price_ex_vat' => 50.00,
                'quantity' => 2,
            ]],

            'remark' => 'This is a customer remark'
        ];
    }
}

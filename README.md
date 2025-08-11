# Abeta for Laravel

The official Laravel plugin for Abeta.
Offer OCI and cXML PunchOut quickly and easily with Abeta. Connect with procurement systems / ERPs such as Coupa, Oracle and Sap Ariba. 
Increase the turnover of existing customers or acquire new customers with the help of B2B connections.

## Installation

Install via composer:

```
composer require abeta-io/laravel
```

### Publish the config file
```
php artisan vendor:publish --tag abeta-config
```

### Publish the routes file
```
php artisan vendor:publish --tag abeta-routes
```

## Quick start

### Return cart Facade
```php
use AbetaIO\Laravel\Models\Product;
use AbetaIO\Laravel\Facades\ReturnCart;

$cart = ReturnCart::builder()->setGeneral([
    'cart_id' => '12345',
    'total' => 150.00,
    'currency' => 'EUR',
    'delivery_datetime' => '2024-11-10 20:29'
]);

// Create Product instance
$product = new Product(
    id: 1,
    sku: 'PROD-001',
    name: 'Product Name',
    description: 'Product description here',
    price_ex_vat: 50.00,
    price_inc_vat: 60.00,
    vat_percentage: 20.00,
    quantity: 2,
    price_unit: 30,     // The amount of units the product price is denominated in.
    unit: 'PCE',        // Unit of the product, e.g. KG, PCE, etc.
    brand: 'Brand Name',
    weight: 1.5,        // Weight in kG or G
    manufacturer_number: 'MANUF-001',
    category_codes: ['47101501'],
    image_url: 'http://example.com/image.jpg',
    categories: ['Category 1', 'Category 2'],
    meta: [ 
        //meta values in preferred array format
    ]
);

// Add product to cart
$cart->addProduct($product);

// Optional: Set a custom return URL instead of using session
ReturnCart::setReturnUrl('http://example.com/custom-return-url');

// Execute the Return Cart
ReturnCart::execute();

// Return Customer to Abeta
return ReturnCart::returnCustomer();

```

### Use the global helper to check if a user is a PunchOut user

```php

if( is_abeta_punchout_user() ) {
    //show button to abeta return cart
} else {
    //show regulal checkout button
}

```

## Order Confirmation Integration
The package provides two approaches for handling order confirmation: event listeners or callback functions. This flexibility simplifies the integration of the order processing flow into various systems.

### Option 1: Using Event Listeners
It is possible to listen to the OrderReceived event to handle order confirmation data. The event provides access to a structured Order DTO, making it easy to retrieve order details.

Example Listener:

```php
namespace App\Listeners;

use AbetaIO\Laravel\Events\OrderReceived;
use Illuminate\Support\Facades\Log;

class HandleOrder
{
    public function handle(OrderReceived $event)
    {
        $order = $event->orderData;

        // Access general order information
        Log::info('Order Received', [
            'Cart ID' => $order->cart_id,
            'Total' => $order->total,
            'Currency' => $order->currency,
        ]);

        // Access billing address
        $billingCity = $order->billTo->city;
        Log::info('Billing City', ['City' => $billingCity]);

        // Access products
        foreach ($order->products as $product) {
            Log::info('Product Details', [
                'Name' => $product->name,
                'Quantity' => $product->quantity,
                'Price' => $product->price_ex_vat,
            ]);
        }
    }
}
```
**Registering the Listener**
By default, Laravel will automatically find and register your event listeners by scanning your application's Listeners directory. However, if event discovery is disabled in your project, you will need to register the listener manually in the EventServiceProvider. To do so, follow these steps:

```php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use AbetaIO\Laravel\Events\OrderReceived;
use App\Listeners\HandleOrder;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderReceived::class => [
            HandleOrder::class,
        ],
    ];
}
```

### Option 2: Using the Callback Function
Alternatively, the onOrderProcessed callback function can be used to handle order data. This approach is useful for inline or quick customizations.

Example Callback inside AppServiceProvider:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use AbetaIO\Laravel\Services\Order\OrderService;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        OrderService::onOrderProcessed(function ($order) {
            Log::info('Order Processed', [
                'Cart ID' => $order->cart_id,
                'Customer Reference' => $order->customer_reference,
                'Total Amount' => $order->total,
            ]);

            foreach ($order->products as $product) {
                Log::info('Product Info', [
                    'Name' => $product->name,
                    'Quantity' => $product->quantity,
                ]);
            }
        });
    }
}
```

### Error Handling
The plugin uses a custom exception, `OrderProcessingException`, to handle errors during order processing. This exception ensures that errors are properly caught and returned as structured messages by the controller.

The plugin validates incoming data in the controller and returns error messages in the response when validation fails. However, you can also implement custom error handling logic in your event listener or callback function.

```php
use AbetaIO\Laravel\Exceptions\OrderProcessingException;

OrderService::onOrderProcessed(function ($order) {
    if (!$order->cart_id) {
        throw new OrderProcessingException('Cart ID is missing.');
    }
});
```

### Order DTO Structure
The Order DTO provides an object-oriented structure for accessing order data:
| Property             | Description                         |
| -------------------- | ----------------------------------- |
| `cart_id`            | Unique identifier for the cart      |
| `total`              | Total order amount                  |
| `currency`           | Order currency (e.g., `EUR`)        |
| `delivery_datetime`  | Delivery date and time              |
| `order_reference`    | Reference number for the order      |
| `customer_reference` | Reference number for the customer   |
| `products`           | Collection of `Product` objects     |
| `billTo`             | Billing address (`Address` object)  |
| `shippTo`            | Shipping address (`Address` object) |


### Converting Order to Array
To convert the Order DTO to an array for further processing:

```php
$orderArray = $order->toArray();
\Log::info('Order as Array', $orderArray);
```

## Configuration
### Customizing Routes
The package provides predefined routes for handling login operations. If you prefer to customize these routes, you can configure the following options in config/abeta.php:

```php
'routes' => [
    'load' => true,
    'prefix' => 'abeta',
    'redirectTo' => '/',
],
```

### Using other model than User model

Want to use another model than Laravels default User model? 

```php

<?php

/*
* Configuration of the Abeta Punchout Package, used to offer OCI and cXML via the Abeta Middleware.
*/

return [

    /*
    * Customer model, used to select customer from.
    */
    'customerModel' => '\App\Models\User',

    /*
    * Auth provider
    */
    'auth' => '\Illuminate\Support\Facades\Auth',

    /*
    * String, representing database column of username
    */
    'username' => 'email',

    /*
    * String, representing database column of password
    */
    'password' => 'password',

];

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
    'currency' => 'EUR'
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
    image_url: 'http://example.com/image.jpg',
    categories: ['Category 1', 'Category 2'],
);

// Add product to cart
$cart->addProduct($product);

// Optional: Set a custom return URL instead of using session
ReturnCart::setReturnUrl('http://example.com/custom-return-url');

// Execute the Return Cart
ReturnCart::execute();

// Return Customer to Abeta
ReturnCart::returnCustomer();

```

### Use the global helper to check if a user is a PunchOut user

```php

if( is_abeta_punchout_user() ) {
    //show button to abeta return cart
} else {
    //show regulal checkout button
}

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

```


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

### Using the class

```php

use AbetaIO\Laravel\AbetaPunchout;

// Don't forget to make the necessary changes to your VerifyCsrfToken file!

//Create an endpoint for a return cart
Route::post('/abeta-cart', function (Request $request) {
    //Return Cart and Product Model or Array to Abeta
    $returned = AbetaPunchout::returnCart($cart, $products);

    //Return Customer to Abeta
    return AbetaPunchout::returnCustomer();
});


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


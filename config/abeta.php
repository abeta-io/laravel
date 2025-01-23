<?php
declare(strict_types=1);

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

    'api_key' => env('ABETA_API_KEY', ''),

    'routes' => [

        /*
        * Whether to automatically register the routes provided by the Abeta Punchout Package.
        */
        'load' => true,

        /*
        * URL prefix for all routes provided by the Abeta Punchout Package.
        */
        'prefix' => 'abeta',

        /*
        * Default URL or route name to redirect users to after certain actions,
        * such as successful authentication or other workflow completions.
        */
        'redirectTo' => '/',
    ],
];

<?php

namespace AbetaIO\Laravel;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

/**
 * @author Abeta 
 * @author info@abeta.nl
 */
class AbetaPunchOut
{
    

    /**
     * Check if customer is logged in via PunchOut
     *
     * @return bool
     */
    public static function isPunchoutUser()
    {
        if (! request()->session()->has('abeta_punchout')) {
            return false;
        }

        if (request()->session()->get('abeta_punchout.user_id') !== self::getAuth()::id()) {
            return false;
        }

        if (request()->session()->has('abeta_punchout.return_url')) {
            return true;
        }

        return false;
    }

    /**
     * Return the cart to Abeta
     *
     * @param Model|Array $cart_general
     * @param Model|Array $products
     * @return Bool
     */
    public static function returnCart($cart_general = [], $products = [])
    {
        $return_url = request()->session()->get('abeta_punchout.return_url');

        $response = Http::timeout(5)
            ->retry(3)
            ->post($return_url, [
                'general' => $cart_general,
                'products' => $products,
            ]);

        return $response->successful();
    }

    /**
     * Return the customer to Abeta
     *
     * @param Model|Array $cart_general
     * @param Model|Array $products
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function returnCustomer()
    {
        $return_url = request()->session()->get('abeta_punchout.return_url');

        request()->session()->flush();

        return redirect($return_url);
    }

    public static function getAuth()
    {
        return config('abeta.auth') ?? '\Illuminate\Support\Facades\Auth';
    }

    public static function getCustomerModel()
    {
        return config('abeta.customerModel') ?? '\App\Models\User';
    }

    public static function getCredentialUsername()
    {
        return config('abeta.username') ?? 'email';
    }

    public static function getCredentialPassword()
    {
        return config('abeta.password') ?? 'password';
    }

    public static function returnResponse($message, $code)
    {
        return response()->json($message, $code);
    }
}

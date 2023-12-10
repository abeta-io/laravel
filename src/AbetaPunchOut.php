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
     * Check if SetupRequest is valid and respond with one_time_url
     *
     * @param Request $request
     * @return Response
     */
    public static function setupRequest(Request $request)
    {
        $username_email = $request->input('username') ?? $request->input('email');
        
        if($request->input('api_key') !== env('ABETA_API_KEY'))
        {
            return self::returnResponse(['message' => 'Api key is invalid'], 404);
        }

        if (! self::getAuth()::validate([
                self::getCredentialUsername() => $username_email, 
                self::getCredentialPassword() => $request->input('password')
            ]))
        {
            return response()->json([
                'message' => 'Credentials seem to be invalid'
            ], 404);
        } else {
            $user = self::getCustomerModel()::where(
                    self::getCredentialUsername(), $username_email
                )->first();
            $url = URL::temporarySignedRoute( 'punchout.login', Carbon::now()->addMinutes(20), ['user_id' => $user->id, 'return_url' => $request->input('return_url')]);
            
            return self::returnResponse(['one_time_url' => $url], 200);
        }
       
        return self::returnResponse(['message' => 'Something went wrong'], 404);
    }

    /**
     * Login customer and set session
     *
     * @param Request $request
     * @return Model
     */
    public static function login(Request $request)
    {
        abort_unless($request->hasValidSignature(), 404);
        
        $user = self::getAuth()::loginUsingId($request->get('user_id'), false);

        session()->put('abeta_punchout', [
            'return_url' => $request->get('return_url'),
            'user_id' => $request->get('user_id')
        ]);

        return $user;
    }

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

        if( request()->session()->get('abeta_punchout.user_id') !== self::getAuth()::id() )
        {
            return false;
        }

        if( request()->session()->has('abeta_punchout.return_url') ){
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

    private static function getAuth()
    {
        return config('abeta.auth') ?? '\Illuminate\Support\Facades\Auth';
    }

    private static function getCustomerModel()
    {
        return config('abeta.customerModel') ?? '\App\Models\User';
    }

    private static function getCredentialUsername()
    {
        return config('abeta.username') ?? 'email';
    }

    private static function getCredentialPassword()
    {
        return config('abeta.password') ?? 'password';
    }

    private static function returnResponse($message, $code)
    {
        return response()->json($message, $code);
    }

}

<?php

namespace AbetaIO\Laravel\Http\Controllers;

use AbetaIO\Laravel\AbetaPunchOut;
use Illuminate\Http\Request;

class LoginController
{
    /**
     * Login customer and set session
     *
     * @param Request $request
     * @return Model
     */
    public function login(Request $request)
    {
        abort_unless($request->hasValidSignature(), 404);
        
        $user = AbetaPunchOut::getAuth()::loginUsingId($request->get('user_id'), false);

        session()->put('abeta_punchout', [
            'return_url' => $request->get('return_url'),
            'user_id' => $request->get('user_id')
        ]);

        // Redirect the user to the configured route after login
        return redirect(config('abeta.routes.redirectTo'));
    }
}
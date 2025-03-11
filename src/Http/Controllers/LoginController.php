<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Http\Controllers;

use AbetaIO\Laravel\AbetaPunchOut;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController
{
    /**
     * Login customer and set session
     *
     * @return Model
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 404);

        $user = AbetaPunchOut::getCustomerModel()::find($request->user_id);

        if ($user) {
            // Log in the user
            AbetaPunchOut::getAuth()::login($user);

            // Store return URL and user_id in the session
            Session::put('abeta_punchout', [
                'return_url' => $request->get('return_url'),
                'user_id' => $user->id,
            ]);

            // Redirect the user to the configured route after login
            return redirect()->intended(config('abeta.routes.redirectTo'));
        }

        return AbetaPunchOut::returnResponse([
            'error' => 'User not found',
            'code' => 404,
        ], 404);
    }
}

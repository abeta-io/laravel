<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Http\Controllers;

use AbetaIO\Laravel\AbetaPunchOut;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PunchOutController
{
    /**
     * Check if SetupRequest is valid and respond with one_time_url
     *
     * @return Response
     */
    public function setupRequest(Request $request): JsonResponse
    {
        // Check if the API key is set in the configuration
        $apiKey = config('abeta.api_key');
        if (blank($apiKey)) {
            return AbetaPunchOut::returnResponse(['message' => 'API key is not set in configuration', 'error' => 500], 500);
        }

        $username_email = $request->json('username') ?? $request->json('email');

        $sentKey = $request->json('api_key');
        if (! is_string($sentKey) || ! hash_equals((string) $apiKey, $sentKey)) {
            return AbetaPunchOut::returnResponse(['message' => 'Api key is invalid', 'error' => 404], 404);
        }

        if (! AbetaPunchOut::getAuth()::validate([
            AbetaPunchOut::getCredentialUsername() => $username_email,
            AbetaPunchOut::getCredentialPassword() => $request->json('password'),
        ])) {
            return AbetaPunchOut::returnResponse(['message' => 'Credentials seem to be invalid', 'error' => 401], 401);
        } else {
            $user = AbetaPunchOut::getCustomerModel()::where(
                AbetaPunchOut::getCredentialUsername(),
                $username_email
            )->first();

            $url = URL::temporarySignedRoute(
                'abeta.login',
                Carbon::now()->addMinute(),
                ['user_id' => $user->id, 'return_url' => $request->json('return_url')]
            );

            return AbetaPunchOut::returnResponse(['one_time_url' => $url], 200);
        }

        return AbetaPunchOut::returnResponse(['message' => 'Something went wrong', 'error' => 404], 404);
    }
}

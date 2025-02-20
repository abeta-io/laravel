<?php

declare(strict_types=1);

namespace AbetaIO\Laravel;

use Illuminate\Http\JsonResponse;

/**
 * @author Abeta
 * @author info@abeta.nl
 */
class AbetaPunchOut
{
    /**
     * Check if customer is logged in via PunchOut
     */
    public static function isPunchoutUser(): bool
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

    public static function getAuth(): string
    {
        return config('abeta.auth') ?? '\Illuminate\Support\Facades\Auth';
    }

    public static function getCustomerModel(): string
    {
        return config('abeta.customerModel') ?? '\App\Models\User';
    }

    public static function getCredentialUsername(): string
    {
        return config('abeta.username') ?? 'email';
    }

    public static function getCredentialPassword(): string
    {
        return config('abeta.password') ?? 'password';
    }

    public static function returnResponse($message, $code): JsonResponse
    {
        return response()->json($message, $code);
    }
}

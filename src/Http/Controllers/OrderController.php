<?php

namespace AbetaIO\Laravel\Http\Controllers;

use AbetaIO\Laravel\AbetaPunchOut;
use AbetaIO\Laravel\Services\Order\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class OrderController
{
    /**
     * Check if SetupRequest is valid and respond with one_time_url
     *
     * @param Request $request
     * @return Response
     */
    public function confirm(Request $request, OrderService $orderService)
    {
        // Check if the API key is set in the configuration
        $apiKey = config('abeta.api_key');
        if (is_null($apiKey)) {
            return AbetaPunchOut::returnResponse(['message' => 'API key is not set in configuration'], 500);
        }

        if ($request->input('api_key') !== $apiKey) {
            return AbetaPunchOut::returnResponse(['message' => 'Api key is invalid'], 404);
        }

        try {
            // Process the incoming order data
            $orderService->processOrder($request->all());

            return AbetaPunchOut::returnResponse([
                'message' => 'Order confirmed successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Handle validation or processing errors
            return AbetaPunchOut::returnResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

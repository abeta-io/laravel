<?php
declare(strict_types=1);

namespace AbetaIO\Laravel\Http\Controllers;

use AbetaIO\Laravel\AbetaPunchOut;
use AbetaIO\Laravel\Exceptions\OrderProcessingException;
use AbetaIO\Laravel\Services\Order\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            return AbetaPunchOut::returnResponse([
                'status' => 'error',
                'message' => 'API key is not set in configuration'
            ], 500);
        }

        if ($request->input('api_key') !== $apiKey) {
            return AbetaPunchOut::returnResponse([
                'status' => 'error',
                'message' => 'Api key is invalid'
            ], 401);
        }

        try {
            // Process the incoming order data
            $orderService->processOrder($request->all());

            return AbetaPunchOut::returnResponse([
                'status' => 'success',
                'message' => 'Order confirmed successfully.',
            ], 200);
        } catch (OrderProcessingException $e) {
            // Handle validation or processing errors
            return AbetaPunchOut::returnResponse([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Order confirmation error: ' . $e->getMessage(), $e->getTrace());

            return AbetaPunchOut::returnResponse([
                'status' => 'error',
                'error' => "Server error",
            ], 503);
        }
    }
}

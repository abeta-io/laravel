<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Services\Cart;

use AbetaIO\Laravel\Exceptions\ReturnCartException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ReturnCart
{
    protected $builder;

    protected ?string $returnUrl = null;

    public function __construct(CartBuilder $builder)
    {
        $this->builder = $builder;

        // Check if session is available and set returnUrl if so
        if (Session::has('abeta_punchout.return_url')) {
            $this->returnUrl = Session::get('abeta_punchout.return_url');
        }
    }

    /**
     * Set or override the return URL.
     *
     * @return $this
     */
    public function setReturnUrl(string $url): self
    {
        $this->returnUrl = $url;

        return $this;
    }

    /**
     * Execute the return cart request.
     *
     * @param  string|null  $returnUrl
     *
     * @throws ReturnCartException
     */
    public function execute(): bool
    {
        // Check if return URL is set, either from session or overridden
        if (is_null($this->returnUrl)) {
            throw new ReturnCartException('The return URL is not set.');
        }

        // Build the cart data using CartBuilder
        $cartData = $this->builder->build();

        // Send the request to the external service
        $response = Http::timeout(5)
            ->retry(3)
            ->post($this->returnUrl, $cartData);

        // If the HTTP request fails, throw a custom exception
        if (! $response->successful()) {
            throw new ReturnCartException;
        }

        return true;
    }

    /**
     * Return the customer to Abeta by redirecting to the return URL.
     */
    public function returnCustomer(): RedirectResponse
    {
        if (is_null($this->returnUrl)) {
            throw new ReturnCartException('The return URL is not set for redirection.');
        }

        // Clear the session data if available
        if (Session::has('abeta_punchout.return_url') || Session::has('abeta_punchout.user_id')) {
            Session::forget(['abeta_punchout.return_url', 'abeta_punchout.user_id']);
        }

        return redirect($this->returnUrl);
    }

    /**
     * Get an instance of CartBuilder to build the cart details.
     */
    public function builder(): CartBuilder
    {
        return $this->builder;
    }
}

<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1;

use PuntoPost\Sdk\Http\CurlHttpClient;
use PuntoPost\Sdk\Http\HttpClientInterface;

class PuntoPostClient
{
    public const SDK_VERSION = '1.0.0';
    public const SDK_HEADER_NAME = 'X-PuntoPost-SDK';
    public const SDK_HEADER_VALUE = 'php/' . self::SDK_VERSION;

    private AuthApi $authApi;
    private MerchantApi $merchantApi;

    public function __construct(string $baseUrl, ?HttpClientInterface $httpClient = null)
    {
        $client = $httpClient ?? new CurlHttpClient();
        $this->authApi = new AuthApi($client, $baseUrl);
        $this->merchantApi = new MerchantApi($client, $baseUrl);
    }

    /**
     * Sets the JWT token manually on all API instances.
     * Useful when you already have a valid token and do not need to log in again.
     */
    public function setToken(string $token): void
    {
        $this->authApi->setToken($token);
        $this->merchantApi->setToken($token);
    }

    /**
     * Clears the JWT token from all API instances.
     */
    public function clearToken(): void
    {
        $this->authApi->setToken(null);
        $this->merchantApi->setToken(null);
    }

    /**
     * Returns the Auth API for direct access to authentication endpoints.
     */
    public function auth(): AuthApi
    {
        return $this->authApi;
    }

    /**
     * Returns the Merchant API for access to all merchant endpoints.
     */
    public function merchant(): MerchantApi
    {
        return $this->merchantApi;
    }
}

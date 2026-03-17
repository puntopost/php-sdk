<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1;

use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\V1\Request\LoginRequest;
use PuntoPost\Sdk\V1\Response\LoginResponse;

class AuthApi extends AbstractApi
{
    /**
     * Authenticates a user and returns a JWT token.
     *
     * @throws ValidationException  on invalid payload (400)
     * @throws PuntoPostException   on bad credentials (401), blocked user (403), or any other error
     */
    public function login(LoginRequest $request): LoginResponse
    {
        $response = $this->post('/api/auth/v1/login', $request->toArray());

        return LoginResponse::fromArray($this->decodeBody($response));
    }
}

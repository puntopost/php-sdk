<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1;

use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\V1\Request\GetParcelTrackingQrRequest;

class WebApi extends AbstractApi
{
    /**
     * Downloads the tracking QR image (PNG) for a parcel by id, tracking number or label.
     * This endpoint is public — no authentication is required.
     *
     * @return string raw PNG bytes
     *
     * @throws ValidationException  on invalid parameters (400)
     * @throws PuntoPostException   on not found (404)
     */
    public function getParcelTrackingQr(GetParcelTrackingQrRequest $request): string
    {
        $path = '/api/web/v1/parcels/qr/' . rawurlencode($request->getIdentifier()) . '.png';
        $response = $this->get($path, [], ['Accept' => 'image/png']);

        return $response->getBody();
    }
}

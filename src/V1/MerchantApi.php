<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1;

use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\V1\Request\CancelParcelRequest;
use PuntoPost\Sdk\V1\Request\CheckCoverageRequest;
use PuntoPost\Sdk\V1\Request\CreateB2CParcelRequest;
use PuntoPost\Sdk\V1\Request\CreateC2BParcelRequest;
use PuntoPost\Sdk\V1\Request\CreateC2CParcelRequest;
use PuntoPost\Sdk\V1\Request\GetMerchantRequest;
use PuntoPost\Sdk\V1\Request\GetParcelRequest;
use PuntoPost\Sdk\V1\Request\GetPudoRequest;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;
use PuntoPost\Sdk\V1\Request\MarkParcelReadyRequest;
use PuntoPost\Sdk\V1\Response\CoverageCheckResponse;
use PuntoPost\Sdk\V1\Response\CoverageListResponse;
use PuntoPost\Sdk\V1\Response\MerchantDetailResponse;
use PuntoPost\Sdk\V1\Response\ParcelDetailResponse;
use PuntoPost\Sdk\V1\Response\PudoDetailResponse;
use PuntoPost\Sdk\V1\Response\PudoListResponse;
use PuntoPost\Sdk\V1\Response\SuccessResponse;

class MerchantApi extends AbstractApi
{
    /**
     * Returns parcel detail by id, tracking number or label.
     *
     * @throws ValidationException  on invalid parameters (400)
     * @throws PuntoPostException   on authentication error (401) or not found (404)
     */
    public function getParcel(GetParcelRequest $request): ParcelDetailResponse
    {
        $path = '/api/merchant/v1/parcels/' . rawurlencode($request->getIdentifier());
        $response = $this->get($path, [], $this->authHeaders());

        return ParcelDetailResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Cancels a parcel by id, tracking number or label.
     *
     * @throws ValidationException  on invalid parameters (400)
     * @throws PuntoPostException   on authentication error (401), not found (404) or incompatible status (409)
     */
    public function cancelParcel(CancelParcelRequest $request): SuccessResponse
    {
        $path = '/api/merchant/v1/parcels/' . rawurlencode($request->getIdentifier());
        $response = $this->delete($path, $this->authHeaders());

        return new SuccessResponse($response->getStatusCode());
    }

    /**
     * Marks a parcel as ready to be picked up.
     *
     * @throws ValidationException  on invalid parameters (400)
     * @throws PuntoPostException   on authentication error (401) or not found (404)
     */
    public function markParcelReady(MarkParcelReadyRequest $request): SuccessResponse
    {
        $path = '/api/merchant/v1/parcels/' . rawurlencode($request->getIdentifier()) . '/ready';
        $response = $this->put($path, $this->authHeaders());

        return new SuccessResponse($response->getStatusCode());
    }

    /**
     * Creates a new C2C parcel (consumer to consumer).
     *
     * @throws ValidationException  on invalid payload (400)
     * @throws PuntoPostException   on authentication error (401)
     */
    public function createC2CParcel(CreateC2CParcelRequest $request): ParcelDetailResponse
    {
        $path = '/api/merchant/v1/' . rawurlencode($request->getMerchantId()) . '/parcels';
        $response = $this->post($path, $request->toArray(), $this->authHeaders());

        return ParcelDetailResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Creates a new B2C parcel (business to consumer).
     *
     * @throws ValidationException  on invalid payload (400)
     * @throws PuntoPostException   on authentication error (401) or pudo not found (404)
     */
    public function createB2CParcel(CreateB2CParcelRequest $request): ParcelDetailResponse
    {
        $path = '/api/merchant/v1/' . rawurlencode($request->getMerchantId()) . '/parcels/b2c';
        $response = $this->post($path, $request->toArray(), $this->authHeaders());

        return ParcelDetailResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Creates a new C2B parcel (consumer to business).
     *
     * @throws ValidationException  on invalid payload (400)
     * @throws PuntoPostException   on authentication error (401)
     */
    public function createC2BParcel(CreateC2BParcelRequest $request): ParcelDetailResponse
    {
        $path = '/api/merchant/v1/' . rawurlencode($request->getMerchantId()) . '/parcels/c2b';
        $response = $this->post($path, $request->toArray(), $this->authHeaders());

        return ParcelDetailResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Returns a geolocated list of PUDOs (pick-up/drop-off points).
     * Use latitude/longitude or postal code. Defaults to Mexico City if omitted.
     *
     * @throws ValidationException  on invalid parameters (400)
     * @throws PuntoPostException   on authentication error (401)
     */
    public function listPudos(?ListPudosRequest $request = null): PudoListResponse
    {
        $params = $request !== null ? $request->toQueryParams() : [];
        $response = $this->get('/api/merchant/v1/pudos', $params, $this->authHeaders());

        return PudoListResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Returns the detail of a PUDO by its id.
     *
     * @throws ValidationException  on invalid parameters (400)
     * @throws PuntoPostException   on authentication error (401) or not found (404)
     */
    public function getPudo(GetPudoRequest $request): PudoDetailResponse
    {
        $path = '/api/merchant/v1/pudos/' . rawurlencode($request->getId());
        $response = $this->get($path, [], $this->authHeaders());

        return PudoDetailResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Returns the detail of a merchant by its id.
     *
     * @throws PuntoPostException on authentication error (401) or not found (404)
     */
    public function getMerchant(GetMerchantRequest $request): MerchantDetailResponse
    {
        $path = '/api/merchant/v1/merchants/' . rawurlencode($request->getId());
        $response = $this->get($path, [], $this->authHeaders());

        return MerchantDetailResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Checks whether a postal code has coverage within the service area.
     *
     * @throws PuntoPostException on invalid parameters (400) or authentication error (401)
     */
    public function checkCoverage(CheckCoverageRequest $request): CoverageCheckResponse
    {
        $path = '/api/merchant/v1/coverage/' . rawurlencode($request->getPostalCode());
        $response = $this->get($path, [], $this->authHeaders());

        return CoverageCheckResponse::fromArray($this->decodeBody($response));
    }

    /**
     * Returns all postal codes that have at least one PUDO nearby.
     *
     * @throws PuntoPostException on authentication error (401)
     */
    public function getCoverageList(): CoverageListResponse
    {
        $response = $this->get('/api/merchant/v1/coverage', [], $this->authHeaders());

        return CoverageListResponse::fromArray($this->decodeBody($response));
    }

    /**
     * @return array<string,string>
     */
    private function authHeaders(): array
    {
        return $this->token !== null
            ? ['Authorization' => 'Bearer ' . $this->token]
            : [];
    }
}

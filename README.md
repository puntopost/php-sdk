# PuntoPost PHP SDK

Official PHP SDK for the PuntoPost API. Integrate parcel delivery services directly into your application.

![CI](https://github.com/puntopost/php-sdk/actions/workflows/ci.yml/badge.svg)

---

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Basic setup](#basic-setup)
- [Authentication](#authentication)
  - [Login](#login)
- [Merchant API](#merchant-api)
  - [Get merchant details](#get-merchant-details)
  - [Check if a postal code has coverage](#check-if-a-postal-code-has-coverage)
  - [Get all postal codes with coverage](#get-all-postal-codes-with-coverage)
  - [List PUDOs by coordinate](#list-pudos-by-coordinate)
  - [List PUDOs by postal code](#list-pudos-by-postal-code)
  - [Get PUDO details](#get-pudo-details)
  - [Create a C2C parcel](#create-a-c2c-parcel-consumer-to-consumer)
  - [Create a B2C parcel](#create-a-b2c-parcel-business-to-consumer)
  - [Create a C2B parcel](#create-a-c2b-parcel-consumer-to-business)
  - [Get parcel details](#get-parcel-details)
  - [Mark a parcel as ready for pickup](#mark-a-parcel-as-ready-for-pickup)
  - [Cancel a parcel](#cancel-a-parcel)
- [Error handling](#error-handling)
- [Custom HTTP client](#custom-http-client)
  - [Symfony HttpClient adapter](#symfony-httpclient-adapter)
  - [Laravel HTTP client adapter](#laravel-http-client-adapter)

---

## Requirements

- PHP >= 7.4 (compatible up to PHP 8.5+)
- `ext-curl` (only required when using the built-in HTTP client)
- `ext-json`

---

## Installation

```bash
composer require puntopost/php-sdk
```

---

## Basic setup

The SDK has no hardcoded base URL. You must specify the target environment (production, sandbox, or your own test
server):

```php
use PuntoPost\Sdk\V1\PuntoPostClient;

$client = new PuntoPostClient('https://api.host.com');
```

The second optional parameter accepts an `HttpClientInterface` instance. When omitted, `CurlHttpClient` is used by
default.

---

## Authentication

### Login

Authenticates the user and stores the JWT automatically for all subsequent requests.

| Parameter  | Type     | Required | Description               |
|------------|----------|----------|---------------------------|
| `username` | `string` | Yes      | Account username or email |
| `password` | `string` | Yes      | Account password          |

```php
use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\LoginRequest;

$client = new PuntoPostClient('https://api.host.com');

try {
    $response = $client->auth()->login(new LoginRequest(
        'my.user',    // username 
        'my_password' // password 
    ));

    echo $response->getToken();     // JWT token to use in subsequent requests
    echo $response->getExpiresIn(); // seconds until the token expires
} catch (ValidationException $e) {
    // HTTP 400 — one or more fields failed validation
    print_r($e->getFieldErrors());
} catch (PuntoPostException $e) {
    // HTTP 401 — wrong credentials, blocked user, etc.
    echo $e->getStatusCode();  // e.g. 401
    echo $e->getErrorType();   // e.g. UNAUTHORIZED
    echo $e->getErrorDetail(); // descriptive message
}
```

If you already have a valid token (e.g. stored in the session), you can set it directly:

```php
$client->setToken('my-saved-jwt');
```

To clear the token:

```php
$client->clearToken();
```

---

## Merchant API

### Get merchant details

| Parameter | Type     | Required | Description                            |
|-----------|----------|----------|----------------------------------------|
| `id`      | `string` | Yes      | Your merchant ID assigned by PuntoPost |

```php
use PuntoPost\Sdk\V1\Request\GetMerchantRequest;

$response = $client->merchant()->getMerchant(new GetMerchantRequest(
    'MERCHANT_ID' // id
));
$merchant = $response->getDetail();

echo $merchant->getId();
echo $merchant->getName();
echo $merchant->isEnabled() ? 'active' : 'inactive';
echo $merchant->isWebhookEnabled() ? 'webhook on' : 'webhook off';
echo $merchant->getWebhookUrl(); // Your webhook url (nullable)

foreach ($merchant->getUsers() as $user) { // Your users with API access
    echo $user->getId();
    echo $user->getUsername();
    echo $user->getEmail();
    echo $user->isEnabled() ? 'active' : 'inactive';
    echo $user->getCreatedAt()->format('Y-m-d H:i:s');
}

foreach ($merchant->getPudos() as $pudo) { // Your registered depots - pickup and drop-off points
    echo $pudo->getId();
    echo $pudo->getExternalId();
    echo $pudo->getName();
    echo $pudo->getPhone();
    echo $pudo->getSchedule();
}
```

### Check if a postal code has coverage

| Parameter    | Type     | Required | Description                           |
|--------------|----------|----------|---------------------------------------|
| `postalCode` | `string` | Yes      | Postal code to check (e.g. `'06600'`) |

```php
use PuntoPost\Sdk\V1\Request\CheckCoverageRequest;

$response = $client->merchant()->checkCoverage(new CheckCoverageRequest(
    '06600' // postalCode
));

if ($response->isCovered()) {
    echo 'Postal code has coverage';
} else {
    echo 'No coverage in that area';
}
```

### Get all postal codes with coverage

```php
$response = $client->merchant()->getCoverageList();

foreach ($response->getPostalCodes() as $postalCode) {
    echo $postalCode . PHP_EOL;
}

// Check membership directly
if ($response->has('06600')) {
    echo 'Covered';
}
```

### List PUDOs by coordinate

Search PUDOs around a geographic point using `ListPudosRequest::byCoordinate()`.

| Parameter    | Type         | Required | Description                                                                        |
|--------------|--------------|----------|------------------------------------------------------------------------------------|
| `coordinate` | `Coordinate` | Yes      | Center point for the search. See `Coordinate` fields below                         |
| `radiusKm`   | `int`        | No       | Search radius in kilometres around the coordinate. Uses the API default if omitted |
| `cursor`     | `Pagination` | No       | Pagination cursor to fetch a specific page. See `Pagination` fields below          |

**`Coordinate`**

| Field       | Type    | Required | Description                   |
|-------------|---------|----------|-------------------------------|
| `latitude`  | `float` | Yes      | Latitude of the center point  |
| `longitude` | `float` | Yes      | Longitude of the center point |

**`Pagination`**

| Field    | Type  | Required | Description                                    |
|----------|-------|----------|------------------------------------------------|
| `offset` | `int` | Yes      | Number of items to skip (0 for the first page) |
| `limit`  | `int` | Yes      | Maximum number of items to return per page     |

```php
use PuntoPost\Sdk\V1\Request\DTO\Coordinate;
use PuntoPost\Sdk\V1\Request\DTO\Pagination;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;

$response = $client->merchant()->listPudos(
    ListPudosRequest::byCoordinate(
        new Coordinate(19.4326, -99.1332), // coordinate — latitude and longitude of the center point
        10                                 // radiusKm — search within 10 km (optional)
    )
);

foreach ($response->getItems() as $pudo) {
    echo $pudo->getId();                          // PUDO ID
    echo $pudo->getExternalId();                  // Short id to display
    echo $pudo->getName();                       
    echo $pudo->getSchedule();                    // opening hours as free text
    echo $pudo->isEnabled() ? 'active' : 'inactive';
    echo $pudo->getCreatedAt()->format('Y-m-d'); 

    // address
    echo $pudo->getAddress()->getPostalCode();
    echo $pudo->getAddress()->getCity();
    echo $pudo->getAddress()->getAddress();       // street and number
    $addressCoord = $pudo->getAddress()->getCoordinate();
    if ($addressCoord !== null) {
        echo $addressCoord->getLatitude();
        echo $addressCoord->getLongitude();
    }
}
```

### List PUDOs by postal code

Search PUDOs within a postal code area using `ListPudosRequest::byPostalCode()`.

| Parameter    | Type         | Required | Description                                                                                |
|--------------|--------------|----------|--------------------------------------------------------------------------------------------|
| `postalCode` | `string`     | Yes      | Postal code to search in (e.g. `'06600'`)                                                  |
| `radiusKm`   | `int`        | No       | Search radius in kilometres around the postal code center. Uses the API default if omitted |
| `cursor`     | `Pagination` | No       | Pagination cursor to fetch a specific page. See `Pagination` fields below                  |

**`Pagination`**

| Field    | Type  | Required | Description                                    |
|----------|-------|----------|------------------------------------------------|
| `offset` | `int` | Yes      | Number of items to skip (0 for the first page) |
| `limit`  | `int` | Yes      | Maximum number of items to return per page     |

```php
use PuntoPost\Sdk\V1\Request\DTO\Pagination;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;

$response = $client->merchant()->listPudos(
    ListPudosRequest::byPostalCode(
        '06600', // postalCode
        5        // radiusKm (optional)
    )
);

// No filters — returns all PUDOs (API default applies)
$response = $client->merchant()->listPudos();
```

**Cursor-based pagination** — `getNext()` returns a ready-to-use `ListPudosRequest` built automatically from the API's
next-page URL. Pass it directly to the next call:

```php
$response = $client->merchant()->listPudos(
    ListPudosRequest::byPostalCode('06600', 5)
);

while ($response->getNext() !== null) {
    $response = $client->merchant()->listPudos($response->getNext());

    foreach ($response->getItems() as $pudo) {
        echo $pudo->getName() . PHP_EOL;
    }
}
```

You can also start pagination manually by passing a `Pagination` cursor as the third argument:

```php
$response = $client->merchant()->listPudos(
    ListPudosRequest::byPostalCode(
        '06600',             // postalCode
        5,                   // radiusKm (optional)
        new Pagination(0, 5) // cursor (optional)
    )
);
```

### Get PUDO details

| Parameter | Type     | Required | Description |
|-----------|----------|----------|-------------|
| `id`      | `string` | Yes      | PUDO ID     |

```php
use PuntoPost\Sdk\V1\Request\GetPudoRequest;

$response = $client->merchant()->getPudo(new GetPudoRequest(
    'PUDO_ID' // id
));
$pudo = $response->getDetail();

echo $pudo->getId();                          // PUDO ID
echo $pudo->getExternalId();                  // Short id to display
echo $pudo->getName();                        // display name
echo $pudo->getSchedule();                    // opening hours as free text
echo $pudo->isEnabled() ? 'active' : 'inactive';
echo $pudo->getCreatedAt()->format('Y-m-d');  
echo $pudo->getAddress()->getPostalCode();
echo $pudo->getAddress()->getCity();
echo $pudo->getAddress()->getAddress();       // street and number
$coordinate = $pudo->getAddress()->getCoordinate();
if ($coordinate !== null) {
    echo $coordinate->getLatitude();
    echo $coordinate->getLongitude();
}
```

### Create a C2C parcel (Consumer to Consumer)

A customer drops off the parcel at an origin PUDO and another customer picks it up at a destination PUDO.

**`CreateC2CParcelRequest`**

| Parameter       | Type                | Required | Description                              |
|-----------------|---------------------|----------|------------------------------------------|
| `merchantId`    | `string`            | Yes      | Your Merchant ID                         |
| `content`       | `ParcelContentData` | Yes      | Description and optional content details |
| `sender`        | `PersonData`        | Yes      | Customer dropping off the parcel         |
| `receiver`      | `PersonData`        | Yes      | Customer picking up the parcel           |
| `destinationId` | `string`            | Yes      | PUDO ID where the receiver will collect  |

**`ParcelContentData`**

| Parameter       | Type            | Required | Description                                               |
|-----------------|-----------------|----------|-----------------------------------------------------------|
| `description`   | `string`        | Yes      | Short description of the parcel contents                  |
| `declaredValue` | `DeclaredValue` | No       | Declared monetary value. See `DeclaredValue` fields below |
| `imageUrl`      | `string`        | No       | URL of an image representing the contents                 |
| `weightKg`      | `float`         | No       | Weight of the parcel in kilograms                         |

**`DeclaredValue`** — use the named constructor `DeclaredValue::mxn(amount)` instead of instantiating directly.

| Field      | Type     | Required | Description                                                                        |
|------------|----------|----------|------------------------------------------------------------------------------------|
| `value`    | `float`  | Yes      | Monetary amount (e.g. `250.0`)                                                     |
| `currency` | `string` | Yes      | Currency code. Currently only `'MXN'` is supported via `DeclaredValue::mxn(value)` |

**`PersonData`**

| Parameter    | Type     | Required | Description                                 |
|--------------|----------|----------|---------------------------------------------|
| `firstName`  | `string` | Yes      | First name                                  |
| `lastName`   | `string` | Yes      | Last name                                   |
| `email`      | `string` | Yes      | Contact email address                       |
| `phone`      | `string` | No       | Contact phone number (e.g. `+525512345678`) |
| `postalCode` | `string` | No       | Postal code of the person's address         |

```php
use PuntoPost\Sdk\V1\Request\CreateC2CParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

$request = new CreateC2CParcelRequest(
    'MERCHANT_ID',                         // merchantId
    new ParcelContentData(
        'Programming book',                // description 
        DeclaredValue::mxn(250.0),         // declaredValue (optional)
        'https://example.com/img.jpg',     // imageUrl (optional)
        1.2                                // weightKg (optional)
    ),
    new PersonData(                        // sender
        'Juan',                            //   firstName
        'García',                          //   lastName
        'juan@example.com',                //   email
        '+525512345678',                   //   phone (optional)
        '06600'                            //   postalCode (optional)
    ),
    new PersonData(                        // receiver
        'Ana',                             //   firstName
        'López',                           //   lastName
        'ana@example.com',                 //   email
        '+525587654321',                   //   phone (optional)
        '44100'                            //   postalCode (optional)
    ),
    'DESTINATION_PUDO_ID'                  // destinationId — Pudo ID
);

$response = $client->merchant()->createC2CParcel($request);
$parcel   = $response->getDetail();

echo $parcel->getId();
echo $parcel->getTracking();
```

> The returned `Parcel` object contains exactly the same fields as the response from [Get parcel details](#get-parcel-details).

### Create a B2C parcel (Business to Consumer)

The merchant drops off the parcel at their origin depot and the customer picks it up at a destination PUDO.

**`CreateB2CParcelRequest`**

| Parameter       | Type                | Required | Description                              |
|-----------------|---------------------|----------|------------------------------------------|
| `merchantId`    | `string`            | Yes      | Your Merchant ID                         |
| `content`       | `ParcelContentData` | Yes      | Description and optional content details |
| `receiver`      | `PersonData`        | Yes      | Customer picking up the parcel           |
| `originId`      | `string`            | Yes      | Your depot - PUDO ID                     |
| `destinationId` | `string`            | Yes      | PUDO ID where the customer collects      |

> `ParcelContentData` and `PersonData` fields are the same as in [C2C](#create-a-c2c-parcel-consumer-to-consumer).

```php
use PuntoPost\Sdk\V1\Request\CreateB2CParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

$request = new CreateB2CParcelRequest(
    'MERCHANT_ID',                      // merchantId
    new ParcelContentData(
        'Smartphone',                   // description
        DeclaredValue::mxn(3500.0)      // declaredValue (optional)
    ),
    new PersonData(                     // receiver
        'María', 'Pérez',
        'maria@example.com',
        '+525511223344'                 // phone (optional)
    ),
    'ORIGIN_PUDO_ID',                   // originId - Your PUDO ID
    'DESTINATION_PUDO_ID'               // destinationId - PUDO ID
);

$response = $client->merchant()->createB2CParcel($request);
$parcel   = $response->getDetail();
```

> The returned `Parcel` object contains exactly the same fields as the response from [Get parcel details](#get-parcel-details).

### Create a C2B parcel (Consumer to Business)

A customer drops off the parcel at an origin PUDO and the merchant picks it at his destination depot.

**`CreateC2BParcelRequest`**

| Parameter       | Type                | Required | Description                              |
|-----------------|---------------------|----------|------------------------------------------|
| `merchantId`    | `string`            | Yes      | Your Merchant ID                         |
| `content`       | `ParcelContentData` | Yes      | Description and optional content details |
| `sender`        | `PersonData`        | Yes      | Customer sending the parcel              |
| `destinationId` | `string`            | Yes      | Your depot - PUDO ID                     |

> `ParcelContentData` and `PersonData` fields are the same as in [C2C](#create-a-c2c-parcel-consumer-to-consumer).

```php
use PuntoPost\Sdk\V1\Request\CreateC2BParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

$request = new CreateC2BParcelRequest(
    'MERCHANT_ID',                  // merchantId
    new ParcelContentData(
        'Product return'            // description
    ),
    new PersonData(                 // sender
        'Carlos', 'Ruiz',
        'carlos@example.com'
    ),
    'DESTINATION_PUDO_ID'           // destinationId - Your PUDO ID
);

$response = $client->merchant()->createC2BParcel($request);
$parcel   = $response->getDetail();
```

> The returned `Parcel` object contains exactly the same fields as the response from [Get parcel details](#get-parcel-details).

### Get parcel details

| Parameter    | Type     | Required | Description                                                         |
|--------------|----------|----------|---------------------------------------------------------------------|
| `identifier` | `string` | Yes      | Parcel ID, tracking number, or label — any of the three is accepted |

```php
use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\V1\Request\GetParcelRequest;

try {
    $response = $client->merchant()->getParcel(new GetParcelRequest(
        'MXT0000000001' // identifier
    ));
    $parcel = $response->getDetail();

    // identifiers & tracking
    echo $parcel->getId();           // parcel ID
    echo $parcel->getTracking();     // tracking number
    echo $parcel->getQrTracking();   // URL of the PNG QR code for tracking
    echo $parcel->getLabel();        // label identifier (nullable)
    echo $parcel->getQrLabel();      // URL of the PNG QR code for the label (nullable)

    // dates
    echo $parcel->getCreatedAt()->format('Y-m-d H:i:s');
    $expireAt = $parcel->getExpireAt();
    echo $expireAt !== null ? $expireAt->format('Y-m-d H:i:s') : 'no expiry'; // nullable

    // status
    echo $parcel->getStatus()->getValue();

    // content
    echo $parcel->getContent()->getDescription();
    echo $parcel->getContent()->getWeightKg();

    // sender
    echo $parcel->getSender()->getFirstName();
    echo $parcel->getSender()->getLastName();
    echo $parcel->getSender()->getEmail();
    echo $parcel->getSender()->getPhone();      // nullable
    echo $parcel->getSender()->getPostalCode(); // nullable

    // receiver
    echo $parcel->getReceiver()->getFirstName();
    echo $parcel->getReceiver()->getLastName();
    echo $parcel->getReceiver()->getEmail();
    echo $parcel->getReceiver()->getPhone();      // nullable
    echo $parcel->getReceiver()->getPostalCode(); // nullable

    // origin PUDO (nullable — absent on B2C parcels)
    $origin = $parcel->getOrigin();
    if ($origin !== null) {
        echo $origin->getId();
        echo $origin->getExternalId();
        echo $origin->getName();
        echo $origin->getDescription();
        echo $origin->getSchedule();
        echo $origin->isEnabled() ? 'active' : 'inactive';
        echo $origin->getCreatedAt()->format('Y-m-d');
        echo $origin->getAddress()->getPostalCode();
        echo $origin->getAddress()->getCity();
        echo $origin->getAddress()->getAddress();
        $originCoord = $origin->getAddress()->getCoordinate(); // nullable
        if ($originCoord !== null) {
            echo $originCoord->getLatitude();
            echo $originCoord->getLongitude();
        }
    }

    // destination PUDO
    $destination = $parcel->getDestination();
    echo $destination->getId();
    echo $destination->getExternalId();
    echo $destination->getName();
    echo $destination->getDescription();
    echo $destination->getSchedule();
    echo $destination->isEnabled() ? 'active' : 'inactive';
    echo $destination->getCreatedAt()->format('Y-m-d');
    echo $destination->getAddress()->getPostalCode();
    echo $destination->getAddress()->getCity();
    echo $destination->getAddress()->getAddress();
    $destCoord = $destination->getAddress()->getCoordinate(); // nullable
    if ($destCoord !== null) {
        echo $destCoord->getLatitude();
        echo $destCoord->getLongitude();
    }

    // status history (chronological list of status transitions)
    foreach ($parcel->getStatusHistory() as $entry) {
        echo $entry->getStatus()->getValue();        // status at that point in time
        echo $entry->getWhen()->format('Y-m-d H:i:s'); // when the transition happened
    }
} catch (PuntoPostException $e) {
    echo $e->getStatusCode(); // 401, 403, 404, etc.
}
```

The `ParcelStatus` object returned by `getStatus()` provides typed helper methods to check each status:

```php
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;

$status = $parcel->getStatus();

if ($status->isDelivered()) {
    echo 'Parcel delivered';
}

// Or compare the raw value against a constant
if ($status->getValue() === ParcelStatus::IN_ORIGIN_POINT) {
    echo 'At origin point';
}
```

**Available statuses**

> **Note:** Statuses prefixed with `RETURN_` and `RETURN_FAIL_` only apply to **C2C parcels**. B2C and C2B shipments will never transition into those states.

| Constant                       | Value                          | Helper method                  | Description                                           |
|--------------------------------|--------------------------------|--------------------------------|-------------------------------------------------------|
| `CREATED`                      | `created`                      | `isCreated()`                  | Parcel registered; not yet at origin PUDO             |
| `IN_ORIGIN_POINT`              | `in_origin_point`              | `isInOriginPoint()`            | Parcel dropped off at the origin PUDO                 |
| `IN_TRANSIT_DEPOT`             | `in_transit_depot`             | `isTransitDepot()`             | In transit between origin PUDO and sorting depot      |
| `IN_DEPOT`                     | `in_depot`                     | `isDepot()`                    | Arrived at sorting depot                              |
| `IN_TRANSIT_DESTINATION`       | `in_transit_destination`       | `isTransitDestinationPoint()`  | In transit from sorting depot to destination PUDO     |
| `IN_DESTINATION_POINT`         | `in_destination_point`         | `isDestinationPoint()`         | Arrived at destination PUDO; awaiting collection      |
| `IN_REROUTED_POINT`            | `in_rerouted_point`            | `isReroutedPoint()`            | Redirected to an alternative PUDO                     |
| `DELIVERED`                    | `delivered`                    | `isDelivered()`                | Collected by the recipient — final state              |
| `RETURN_IN_DESTINATION_POINT`  | `return_in_destination_point`  | `isReturnInDestinationPoint()` | Return initiated; parcel at the destination PUDO      |
| `RETURN_IN_TRANSIT_DEPOT`      | `return_in_transit_depot`      | `isReturnInTransitDepot()`     | Return in transit to sorting depot                    |
| `RETURN_IN_DEPOT`              | `return_in_depot`              | `isReturnIndDepot()`           | Return arrived at sorting depot                       |
| `RETURN_IN_TRANSIT_ORIGIN`     | `return_in_transit_origin`     | `isReturnInTransitOrigin()`    | Return in transit from depot to origin PUDO           |
| `RETURN_IN_ORIGIN_POINT`       | `return_in_origin_point`       | `isReturnInOriginPoint()`      | Return arrived at origin PUDO                         |
| `RETURN_IN_REROUTED_POINT`     | `return_in_rerouted_point`     | `isReturnInReroutedPoint()`    | Return redirected to an alternative PUDO              |
| `RETURN_DELIVERED`             | `return_delivered`             | `isReturnDelivered()`          | Return collected by the merchant — final return state |
| `RETURN_FAIL_IN_ORIGIN_POINT`  | `return_fail_in_origin_point`  | `isReturnFailInOriginPoint()`  | Return failed; parcel held at origin PUDO             |
| `RETURN_FAIL_IN_TRANSIT_DEPOT` | `return_fail_in_transit_depot` | `isReturnFailInTransitDepot()` | Return failed; parcel in transit to depot             |
| `RETURN_FAIL_IN_DEPOT`         | `return_fail_in_depot`         | `isReturnFailInDepot()`        | Return failed; parcel held at depot                   |
| `RETURN_FAIL_DELIVERED`        | `return_fail_delivered`        | `isReturnFailDelivered()`      | Return failed but delivered back — review required    |
| `INCIDENCE`                    | `incidence`                    | `isIncidence()`                | An issue has been flagged on this parcel              |
| `CANCELLED`                    | `cancelled`                    | `isCancelled()`                | Parcel cancelled - final state                        |
| `LOST`                         | `lost`                         | `isLost()`                     | Parcel reported as lost - final incidence state       |

### Mark a parcel as ready for pickup

Notifies the system that the parcel is prepared and ready to be collected at the origin PUDO. Only valid when the parcel
is in `created` status.

> **Note:** This action is only available with B2C shipments.

| Parameter    | Type     | Required | Description                                                         |
|--------------|----------|----------|---------------------------------------------------------------------|
| `identifier` | `string` | Yes      | Parcel ID, tracking number, or label — any of the three is accepted |

```php
use PuntoPost\Sdk\V1\Request\MarkParcelReadyRequest;

$response = $client->merchant()->markParcelReady(new MarkParcelReadyRequest(
    'MXT0000000001' // identifier — parcel ID, tracking number, or label
));

echo $response->getStatusCode(); // 204
echo $response->isSuccess();     // true
```

### Cancel a parcel

Cancels a parcel. Only valid while the parcel has not yet entered transit (i.e. before it leaves the origin PUDO).

| Parameter    | Type     | Required | Description                                                         |
|--------------|----------|----------|---------------------------------------------------------------------|
| `identifier` | `string` | Yes      | Parcel ID, tracking number, or label — any of the three is accepted |

```php
use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\V1\Request\CancelParcelRequest;

try {
    $response = $client->merchant()->cancelParcel(new CancelParcelRequest(
        'MXT0000000001' // identifier — parcel ID, tracking number, or label
    ));
    echo $response->getStatusCode(); // 204
} catch (PuntoPostException $e) {
    if ($e->getStatusCode() === 409) {
        // Parcel is already in transit or delivered — cannot be cancelled
        echo $e->getErrorType(); // e.g. STATUS_CONFLICT
    }
}
```

---

## Error handling

All exceptions extend `PuntoPostException`. There are only two types:

| Class                 | When thrown                                      |
|-----------------------|--------------------------------------------------|
| `ValidationException` | HTTP 400 with field-level validation errors      |
| `PuntoPostException`  | Any other API error (401, 403, 404, 409, 5xx, …) |

All string properties default to `''` when the API response does not include them.

```php
use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;

try {
    $response = $client->merchant()->createC2CParcel($request);
} catch (ValidationException $e) {
    // Field-level errors
    foreach ($e->getFieldErrors() as $field => $message) {
        echo "{$field}: {$message}" . PHP_EOL;
    }
    echo $e->getErrorDetail(); // general validation message
} catch (PuntoPostException $e) {
    echo $e->getStatusCode();    // HTTP status code
    echo $e->getErrorType();     // e.g. UNAUTHORIZED, FORBIDDEN, NOT_FOUND ('' if absent)
    echo $e->getErrorTitle();    // error title ('' if absent)
    echo $e->getErrorDetail();   // error description ('' if absent)
    echo $e->getErrorInstance(); // context: authentication, parcel, etc. ('' if absent)
    echo $e->getRawBody();       // raw response body
}
```

---

## Custom HTTP client

The SDK is designed so that you can replace the HTTP client with the one from your framework by implementing
`HttpClientInterface`:

```php
namespace PuntoPost\Sdk\Http;

interface HttpClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): HttpResponse;
}
```

### Symfony HttpClient adapter

Install `symfony/http-client` if you haven't already:

```bash
composer require symfony/http-client
```

Create the adapter in your project:

```php
<?php

namespace App\PuntoPost;

use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;

class SymfonyHttpClientAdapter implements HttpClientInterface
{
    private SymfonyHttpClientInterface $client;

    public function __construct(SymfonyHttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): HttpResponse {
        $options = ['headers' => $headers];

        if ($body !== null) {
            $options['body'] = $body;
        }

        $response = $this->client->request($method, $url, $options);

        return new HttpResponse(
            $response->getStatusCode(),
            $response->getContent(false),
            $response->getHeaders(false)
        );
    }
}
```

Use it in Symfony with dependency injection:

```php
use PuntoPost\Sdk\V1\PuntoPostClient;
use App\PuntoPost\SymfonyHttpClientAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MyService
{
    private PuntoPostClient $puntoPost;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->puntoPost = new PuntoPostClient(
            'https://api.host.com',
            new SymfonyHttpClientAdapter($httpClient)
        );
    }
}
```

Or register it in `services.yaml`:

```yaml
App\PuntoPost\SymfonyHttpClientAdapter:
  arguments:
    $client: '@http_client'

App\MyService:
  arguments:
    $httpClient: '@App\PuntoPost\SymfonyHttpClientAdapter'
```

### Laravel HTTP client adapter

```php
<?php

namespace App\PuntoPost;

use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;
use Illuminate\Http\Client\Factory as HttpFactory;

class LaravelHttpClientAdapter implements HttpClientInterface
{
    private HttpFactory $http;

    public function __construct(HttpFactory $http)
    {
        $this->http = $http;
    }

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): HttpResponse {
        $response = $this->http
            ->withHeaders($headers)
            ->send(strtoupper($method), $url, ['body' => $body]);

        return new HttpResponse(
            $response->status(),
            $response->body(),
            $response->headers()
        );
    }
}
```

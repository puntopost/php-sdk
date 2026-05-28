<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD, PP_MERCHANT_ID

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\CreateC2BParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

$client = create_client();
$merchantId = env_required('PP_MERCHANT_ID');
$destinationId = prompt('Destination PUDO ID (your depot)');
$merchantReference = prompt_optional('Merchant reference');

$request = new CreateC2BParcelRequest(
    $merchantId,
    new ParcelContentData('SDK example C2B return'),
    new PersonData('Carlos', 'Ruiz', 'carlos@example.com'),
    $destinationId,
    $merchantReference
);

$response = $client->merchant()->createC2BParcel($request);

dump_pretty($response);

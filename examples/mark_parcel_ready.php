<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\MarkParcelReadyRequest;

$client = create_client();
$parcelId = prompt('Parcel id, tracking or label');

$response = $client->merchant()->markParcelReady(new MarkParcelReadyRequest($parcelId));

dump_pretty($response);

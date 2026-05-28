<?php

declare(strict_types=1);

// Required env: HOST, USERNAME, PASSWORD

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\GetParcelRequest;

$client = create_client();
$parcelId = prompt('Parcel id, tracking or label');

$response = $client->merchant()->getParcel(new GetParcelRequest($parcelId));

dump_pretty($response);

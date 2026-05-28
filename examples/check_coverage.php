<?php

declare(strict_types=1);

// Required env: HOST, USERNAME, PASSWORD

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\CheckCoverageRequest;

$client = create_client();
$postalCode = prompt('Postal code');

$response = $client->merchant()->checkCoverage(new CheckCoverageRequest($postalCode));

dump_pretty($response);

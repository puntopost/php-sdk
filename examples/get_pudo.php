<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\GetPudoRequest;

$client = create_client();
$pudoId = prompt('Pudo ID');

$response = $client->merchant()->getPudo(new GetPudoRequest($pudoId));

dump_pretty($response);

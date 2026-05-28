<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\LoginRequest;

$host = env_optional('PP_HOST', 'https://localhost');
$username = env_required('PP_USERNAME');
$password = env_required('PP_PASSWORD');

$client = new PuntoPostClient($host, new InsecureCurlHttpClient());
$response = $client->auth()->login(new LoginRequest($username, $password));

dump_pretty($response);

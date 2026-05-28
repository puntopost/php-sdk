<?php

declare(strict_types=1);

// Public endpoint — no PP_USERNAME / PP_PASSWORD required.

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;
use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\GetParcelTrackingQrRequest;

$host = env_optional('PP_HOST', 'https://localhost');
/** @var HttpClientInterface $http */
$http = new InsecureCurlHttpClient();
$client = new PuntoPostClient($host, $http);

$identifier = prompt('Parcel id, tracking or label');
// Default lives inside /app (the mounted project root) so the file is visible on the host.
$outDir = prompt_optional('Output directory', '/app/examples/labels');

$png = $client->web()->getParcelTrackingQr(new GetParcelTrackingQrRequest($identifier));

if (!is_dir($outDir) && !mkdir($outDir, 0777, true) && !is_dir($outDir)) {
    fwrite(STDERR, "ERROR: could not create directory {$outDir}\n");
    exit(1);
}

$path = rtrim($outDir, '/') . "/{$identifier}-qr.png";
file_put_contents($path, $png);

dump_pretty([
    'bytes' => strlen($png),
    'savedTo' => $path,
]);

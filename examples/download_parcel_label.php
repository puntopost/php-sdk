<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\GetParcelLabelRequest;
use PuntoPost\Sdk\V1\Request\GetParcelRequest;

$client = create_client();
$parcelId = prompt('Parcel id, tracking or label (must be a B2C parcel)');
// Default lives inside /app (the mounted project root) so the file is visible on the host.
$outDir = prompt_optional('Output directory', '/app/examples/labels');

$parcelResponse = $client->merchant()->getParcel(new GetParcelRequest($parcelId));
$label = $parcelResponse->getDetail()->getLabel();

if ($label === null) {
    fwrite(STDERR, "ERROR: parcel {$parcelId} has no label — label download is only available for B2C parcels\n");
    exit(1);
}

$labelResponse = $client->merchant()->getParcelLabel(
    GetParcelLabelRequest::fromParcelResponse($parcelResponse)
);

if (!is_dir($outDir) && !mkdir($outDir, 0777, true) && !is_dir($outDir)) {
    fwrite(STDERR, "ERROR: could not create directory {$outDir}\n");
    exit(1);
}

$path = rtrim($outDir, '/') . "/{$label}.{$labelResponse->getExtension()}";
file_put_contents($path, $labelResponse->getContent());

dump_pretty([
    'contentType' => $labelResponse->getContentType(),
    'extension' => $labelResponse->getExtension(),
    'bytes' => strlen($labelResponse->getContent()),
    'savedTo' => $path,
]);

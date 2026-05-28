<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD, PP_MERCHANT_ID

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\CreateB2CParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;
use PuntoPost\Sdk\V1\Request\GetParcelLabelRequest;
use PuntoPost\Sdk\V1\Request\GetParcelTrackingQrRequest;

$client = create_client();
$merchantId = env_required('PP_MERCHANT_ID');
$originId = prompt('Origin PUDO ID (your depot)');
$destinationId = prompt('Destination PUDO ID');
$merchantReference = prompt_optional('Merchant reference');

$request = new CreateB2CParcelRequest(
    $merchantId,
    new ParcelContentData('SDK example B2C parcel', DeclaredValue::mxn(3500.0)),
    new PersonData('María', 'Pérez', 'maria@example.com', '+525511223344'),
    $originId,
    $destinationId,
    $merchantReference
);

$response = $client->merchant()->createB2CParcel($request);
dump_pretty($response);

// Chain: download the label that the API just generated.
$labelResponse = $client->merchant()->getParcelLabel(
    GetParcelLabelRequest::fromParcelResponse($response)
);
$outDir = '/app/examples/labels';
if (!is_dir($outDir) && !mkdir($outDir, 0777, true) && !is_dir($outDir)) {
    fwrite(STDERR, "ERROR: could not create directory {$outDir}\n");
    exit(1);
}
$path = "{$outDir}/{$response->getDetail()->getLabel()}.{$labelResponse->getExtension()}";
file_put_contents($path, $labelResponse->getContent());

dump_pretty([
    'contentType' => $labelResponse->getContentType(),
    'extension' => $labelResponse->getExtension(),
    'bytes' => strlen($labelResponse->getContent()),
    'savedTo' => $path,
]);

// Chain: download the tracking QR that the API just generated.
$qrPng = $client->web()->getParcelTrackingQr(
    GetParcelTrackingQrRequest::fromParcelResponse($response)
);
$qrPath = "{$outDir}/{$response->getDetail()->getTracking()}-qr.png";
file_put_contents($qrPath, $qrPng);

dump_pretty([
    'bytes' => strlen($qrPng),
    'savedTo' => $qrPath,
]);

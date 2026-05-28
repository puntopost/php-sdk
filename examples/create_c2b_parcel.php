<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD, PP_MERCHANT_ID

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\CreateC2BParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;
use PuntoPost\Sdk\V1\Request\GetParcelTrackingQrRequest;

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

// Chain: download the tracking QR that the API just generated.
$qrPng = $client->web()->getParcelTrackingQr(
    GetParcelTrackingQrRequest::fromParcelResponse($response)
);
$outDir = '/app/examples/labels';
if (!is_dir($outDir) && !mkdir($outDir, 0777, true) && !is_dir($outDir)) {
    fwrite(STDERR, "ERROR: could not create directory {$outDir}\n");
    exit(1);
}
$qrPath = "{$outDir}/{$response->getDetail()->getTracking()}-qr.png";
file_put_contents($qrPath, $qrPng);

dump_pretty([
    'bytes' => strlen($qrPng),
    'savedTo' => $qrPath,
]);

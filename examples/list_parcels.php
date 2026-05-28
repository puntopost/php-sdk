<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD, PP_MERCHANT_ID
// All filters are prompted interactively (press Enter to skip each one).

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\ListMerchantParcelsRequest;

$client = create_client();
$merchantId = env_required('PP_MERCHANT_ID');

$dateMin = prompt_optional('Date min (YYYY-MM-DD)');
$dateMax = prompt_optional('Date max (YYYY-MM-DD)');
$statusesRaw = prompt_optional('Statuses (comma-separated, e.g. created,delivered)');
$query = prompt_optional('Free-text query');
$limit = prompt_optional('Limit');
$offset = prompt_optional('Offset');

$statuses = $statusesRaw !== null ? array_map('trim', explode(',', $statusesRaw)) : [];

$request = new ListMerchantParcelsRequest(
    $merchantId,
    $dateMin !== null ? new DateTimeImmutable($dateMin) : null,
    $dateMax !== null ? new DateTimeImmutable($dateMax) : null,
    $statuses,
    $query,
    $limit !== null ? (int) $limit : null,
    $offset !== null ? (int) $offset : null
);

$response = $client->merchant()->listMerchantParcels($request);

dump_pretty($response);

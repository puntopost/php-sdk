<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD, PP_MERCHANT_ID

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\GetMerchantRequest;

$client = create_client();
$merchantId = env_required('PP_MERCHANT_ID');

$response = $client->merchant()->getMerchant(new GetMerchantRequest($merchantId));

dump_pretty($response);

<?php

declare(strict_types=1);

// Required env: PP_HOST, PP_USERNAME, PP_PASSWORD

require __DIR__ . '/bootstrap.php';

$client = create_client();

$response = $client->merchant()->getCoverageList();

dump_pretty($response);

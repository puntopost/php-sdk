<?php

declare(strict_types=1);

// Required env: HOST, USERNAME, PASSWORD

require __DIR__ . '/bootstrap.php';

$client = create_client();

$response = $client->merchant()->getCoverageList();

dump_pretty($response);

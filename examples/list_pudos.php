<?php

declare(strict_types=1);

// Required env: HOST, USERNAME, PASSWORD
// Interactively prompts for an optional filter (postal code or coordinates)
// and an optional search radius.

require __DIR__ . '/bootstrap.php';

use PuntoPost\Sdk\V1\Request\DTO\Coordinate;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;

$client = create_client();

$mode = prompt_optional('Filter by [p]ostal code, [c]oordinate, or [n]one', 'n');
$radiusInput = prompt_optional('Search radius in km');
$radius = $radiusInput !== null ? (int) $radiusInput : null;

if (strtolower($mode) === 'p') {
    $request = ListPudosRequest::byPostalCode(prompt('Postal code'), $radius);
} elseif (strtolower($mode) === 'c') {
    $request = ListPudosRequest::byCoordinate(
        new Coordinate((float) prompt('Latitude'), (float) prompt('Longitude')),
        $radius
    );
} else {
    $request = null;
}

$response = $client->merchant()->listPudos($request);

dump_pretty($response);

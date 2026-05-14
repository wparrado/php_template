<?php
require __DIR__ . '/vendor/autoload.php';
$analysis = \OpenApi\scan([__DIR__ . '/docs']);
file_put_contents('storage/api-docs/scan-output.json', $analysis->toJson());
echo "Wrote storage/api-docs/scan-output.json\n";

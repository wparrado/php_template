<?php
require __DIR__ . '/vendor/autoload.php';
use OpenApi\Generator;
try {
    $gen = new Generator();
    $openapi = $gen->generate([__DIR__.'/docs']);
    if ($openapi) {
        echo "Generated\n";
        file_put_contents(__DIR__.'/storage/api-docs/scan-generated.json', $openapi->toJson());
    } else {
        echo "No OpenAPI generated\n";
    }
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
}

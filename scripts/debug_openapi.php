<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;
use OpenApi\Analysis;

$paths = [__DIR__ . '/../docs', __DIR__ . '/../src', __DIR__ . '/../app'];
echo "Scanning paths:\n" . implode("\n", $paths) . "\n\n";
$generator = new Generator(new \Psr\Log\NullLogger());

// Run generate but catch warnings by setting validation false to avoid exceptions
$context = new \OpenApi\Context(['logger' => new \Psr\Log\NullLogger()]);
$analysis = new Analysis([], $context);
try {
    $openapi = $generator->generate($paths, $analysis, false);
} catch (Throwable $e) {
    echo "Generator threw: " . $e->getMessage() . "\n";
}

// List all annotations discovered in the analysis
$summary = [];
foreach ($analysis->annotations as $annotation) {
    $class = is_object($annotation) ? get_class($annotation) : gettype($annotation);
    $summary[$class] = ($summary[$class] ?? 0) + 1;
}
echo "Total annotation objects: " . array_sum($summary) . "\n";
foreach ($summary as $class => $count) {
    echo "  $class: $count\n";
}

// If OpenApi root present, dump its info and paths counts
foreach ($analysis->annotations as $annotation) {
    if (is_object($annotation) && get_class($annotation) === 'OpenApi\\Annotations\\OpenApi') {
        echo "Found OpenApi root. Paths: ";
        $pathsAnnot = $annotation->paths ?? null;
        if (is_array($pathsAnnot)) {
            echo count($pathsAnnot) . "\n";
            foreach ($pathsAnnot as $p) {
                echo "  path: " . ($p->path ?? '(no path)') . "\n";
            }
        } else {
            echo "(none)\n";
        }
    }
}

echo "Done.\n";

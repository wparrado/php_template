<?php
// Scans PHP files under src and app for docblocks containing @OA or @OpenApi
$dirs = [__DIR__ . '/../src', __DIR__ . '/../app', __DIR__ . '/../docs'];

function scanFile($file) {
    $content = file_get_contents($file);
    if ($content === false) return;
    $tokens = token_get_all($content);
    $line = 1;
    $found = [];
    foreach ($tokens as $tok) {
        if (is_array($tok)) {
            [$id, $text, $tokLine] = $tok + [null, null, null];
            if ($id === T_DOC_COMMENT) {
                foreach (['@OA\\\\', '@OA\\', '@OpenApi\\'] as $needle) {
                    if (stripos($text, '@OA') !== false || stripos($text, '@OpenApi') !== false) {
                        // compute snippet lines
                        $lines = explode("\n", $text);
                        $first = $tokLine;
                        $snippet = implode("\n", array_map(function($l){ return "    " . trim($l); }, $lines));
                        $found[] = ['line' => $first, 'snippet' => $snippet];
                        break;
                    }
                }
            }
            $line = $tokLine ?: $line;
        } else {
            $line += substr_count($tok, "\n");
        }
    }
    if ($found) {
        echo "FILE: $file\n";
        foreach ($found as $f) {
            echo "  DOC at line {$f['line']}:\n";
            echo $f['snippet'] . "\n\n";
        }
    }
}

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if (!$file->isFile()) continue;
        if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) !== 'php') continue;
        scanFile($file->getPathname());
    }
}

echo "Done.\n";

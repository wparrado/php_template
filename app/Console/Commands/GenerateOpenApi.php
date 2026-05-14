<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class GenerateOpenApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openapi:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OpenAPI JSON using zircote/swagger-php';

    public function handle(): int
    {
        $paths = [base_path('docs'), base_path('src'), base_path('app')];
        $this->info('Scanning: ' . implode(', ', $paths));

        // Use NullLogger to avoid trigger_error -> ErrorException escalation from DefaultLogger
        $generator = new \OpenApi\Generator(new \Psr\Log\NullLogger());
        $openapi = $generator->generate($paths, null, true);

        if ($openapi) {
            $target = storage_path('api-docs/api-docs.json');
            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0755, true);
            }
            $openapi->saveAs($target);

            // Merge components from docs/components_manual.json if present to ensure $ref targets exist
            $componentsPath = base_path('docs/components_manual.json');
            if (file_exists($componentsPath)) {
                $generated = json_decode(file_get_contents($target), true);
                $manual = json_decode(file_get_contents($componentsPath), true);

                if (is_array($manual) && isset($manual['components'])) {
                    if (! isset($generated['components'])) {
                        $generated['components'] = $manual['components'];
                    } else {
                        // Merge schemas without overwriting existing ones
                        foreach (($manual['components']['schemas'] ?? []) as $name => $schema) {
                            if (! isset($generated['components']['schemas'][$name])) {
                                $generated['components']['schemas'][$name] = $schema;
                            }
                        }
                    }

                    file_put_contents($target, json_encode($generated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
            }

            $this->info("Wrote {$target}");
            return 0;
        }

        $this->error('No OpenAPI generated');
        return 1;
    }
}

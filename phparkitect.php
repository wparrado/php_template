<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\RuleBuilders\Architecture\Architecture;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__ . '/src');

    $layeredArchitectureRules = Architecture::withComponents()
        ->component('Domain')->definedBy('Domain\\*')
        ->component('Application')->definedBy('Application\\*')
        ->component('Infrastructure')->definedBy('Infrastructure\\*')
        ->component('Presentation')->definedBy('Presentation\\*')

        ->where('Domain')->shouldNotDependOnAnyComponent()
        ->where('Application')->mayDependOnComponents('Domain')
        ->where('Infrastructure')->mayDependOnComponents('Domain', 'Application')
        ->where('Presentation')->mayDependOnComponents('Application', 'Infrastructure')

        ->rules();

    $config->add($classSet, ...$layeredArchitectureRules);
};

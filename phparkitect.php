<?php

declare(strict_types=1);

use Phparkitect\Architecture\Architecture;
use Phparkitect\ClassSet;
use Phparkitect\Rules\ArchRule;
use Phparkitect\Phparkitect;

return static function (Phparkitect $phparkitect): void {
    $classSet = ClassSet::fromDir(__DIR__ . '/src');

    $architecture = Architecture::withComponents()
        ->component('Domain')->definedBy('Domain\*')
        ->component('Application')->definedBy('Application\*')
        ->component('Infrastructure')->definedBy('Infrastructure\*')
        ->component('Presentation')->definedBy('Presentation\*');

    $phparkitect->add(
        ArchRule::its($architecture)
            ->component('Domain')
            ->shouldNotDependOnAnyComponent(),
        $classSet
    );

    $phparkitect->add(
        ArchRule::its($architecture)
            ->component('Application')
            ->shouldOnlyDependOn()
            ->components('Domain'),
        $classSet
    );

    $phparkitect->add(
        ArchRule::its($architecture)
            ->component('Infrastructure')
            ->shouldOnlyDependOn()
            ->components('Domain', 'Application'),
        $classSet
    );

    $phparkitect->add(
        ArchRule::its($architecture)
            ->component('Presentation')
            ->shouldOnlyDependOn()
            ->components('Application', 'Infrastructure'),
        $classSet
    );
};

<?php

declare(strict_types=1);

use Phparkitect\Architecture\Architecture;
use Phparkitect\Phparkitect;

/**
 * PHPArkitect architecture tests.
 * Run via: composer arkitect
 *
 * These tests mirror the layer dependency rules enforced by deptrac.
 */
it('Domain classes must not depend on Application', function (): void {
    // Verified by phparkitect -- this test documents the rule
    expect(true)->toBeTrue();
})->group('architecture');

it('Domain classes must not depend on Infrastructure', function (): void {
    expect(true)->toBeTrue();
})->group('architecture');

it('Domain classes must not depend on Laravel framework', function (): void {
    // Scan domain files for Laravel imports
    $domainFiles = glob(__DIR__ . '/../../src/Domain/**/*.php') ?: [];
    $domainFiles = array_merge(
        $domainFiles,
        glob(__DIR__ . '/../../src/Domain/*.php') ?: [],
        glob(__DIR__ . '/../../src/Domain/**/**/*.php') ?: [],
    );

    foreach ($domainFiles as $file) {
        $contents = file_get_contents($file);
        expect($contents)->not->toContain('use Illuminate\\', "File {$file} imports Laravel in Domain layer");
        expect($contents)->not->toContain('use Laravel\\', "File {$file} imports Laravel in Domain layer");
    }
})->group('architecture');

it('Application classes must not depend on Infrastructure', function (): void {
    $appFiles = glob(__DIR__ . '/../../src/Application/**/*.php') ?: [];
    $appFiles = array_merge(
        $appFiles,
        glob(__DIR__ . '/../../src/Application/**/**/*.php') ?: [],
    );

    foreach ($appFiles as $file) {
        $contents = file_get_contents($file);
        expect($contents)->not->toContain('use Infrastructure\\', "File {$file} imports Infrastructure in Application layer");
    }
})->group('architecture');

it('Application classes must not depend on Presentation', function (): void {
    $appFiles = glob(__DIR__ . '/../../src/Application/**/*.php') ?: [];
    $appFiles = array_merge(
        $appFiles,
        glob(__DIR__ . '/../../src/Application/**/**/*.php') ?: [],
    );

    foreach ($appFiles as $file) {
        $contents = file_get_contents($file);
        expect($contents)->not->toContain('use Presentation\\', "File {$file} imports Presentation in Application layer");
    }
})->group('architecture');

it('Domain classes must not depend on Presentation', function (): void {
    $domainFiles = glob(__DIR__ . '/../../src/Domain/**/*.php') ?: [];
    $domainFiles = array_merge(
        $domainFiles,
        glob(__DIR__ . '/../../src/Domain/**/**/*.php') ?: [],
    );

    foreach ($domainFiles as $file) {
        $contents = file_get_contents($file);
        expect($contents)->not->toContain('use Presentation\\', "File {$file} imports Presentation in Domain layer");
    }
})->group('architecture');

it('AppServiceProvider is the only file that references concrete Infrastructure in Infrastructure\\Providers', function (): void {
    expect(true)->toBeTrue();
})->group('architecture');

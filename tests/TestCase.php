<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base TestCase for project tests.
 *
 * Declare common properties used in tests so static analysis (PHPStan)
 * doesn't complain about dynamic test properties.
 *
 * @property mixed $clock
 * @property mixed $repository
 * @property mixed $handler
 * @property mixed $eventPublisher
 * @property mixed $uow
 * @property mixed $createHandler
 * @property mixed $deleteHandler
 * @property mixed $updateHandler
 * @property mixed $mapper
 */
abstract class TestCase extends BaseTestCase {}

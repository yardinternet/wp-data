<?php

declare(strict_types=1);

namespace Yard\Data\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        \WP_Mock::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \WP_Mock::tearDown();
    }

    /**
     * Get package providers.
     *
     * @param Application  $app
     *
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            'Yard\Data\Providers\DataServiceProvider',
        ];
    }
}

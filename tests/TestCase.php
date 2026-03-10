<?php

namespace AppccDigital\LaravelAppcc\Tests;

use AppccDigital\LaravelAppcc\AppccServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            AppccServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('appcc.url', 'https://test.appcc.digital');
        $app['config']->set('appcc.token', 'test-token');
        $app['config']->set('appcc.timeout', 10);
    }
}

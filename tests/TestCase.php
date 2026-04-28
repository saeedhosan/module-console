<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SaeedHosan\Module\Console\ModuleConsoleServiceProvider;
use SaeedHosan\Module\Support\ServiceProvider as ModuleSupportServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ModuleConsoleServiceProvider::class,
            ModuleSupportServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('cache.default', 'array');
        $app['config']->set('module', [
            'directory' => 'modules',
            'lowercase' => true,
            'namespace' => 'Modules',
        ]);
    }
}

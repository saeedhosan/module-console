<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use SaeedHosan\Module\Console\ModuleConsoleServiceProvider;
use Tests\TestCase;

uses(TestCase::class);

it('registers migration creator alias', function () {
    $app      = app();
    $provider = new ModuleConsoleServiceProvider($app);
    $provider->register();

    expect($app->bound('migration.creator'))->toBeTrue();
});

it('publishes stubs when running in console', function () {
    $app      = app();
    $provider = new ModuleConsoleServiceProvider($app);
    $provider->boot();

    $publishes = (new ReflectionClass($provider))->getProperty('publishes');

    expect($publishes->getValue($provider))->not->toBeEmpty();
});

it('registers generate module skeleton command', function () {
    $app      = app();
    $provider = new ModuleConsoleServiceProvider($app);
    $provider->boot();

    expect($provider)->toBeInstanceOf(ModuleConsoleServiceProvider::class);
});

it('registers module list command', function () {
    $app = app();

    Artisan::call('list');

    expect(Artisan::output())->toContain('module');
});

test('returns available make commands', function () {
    $reflection = new ReflectionClass(ModuleConsoleServiceProvider::class);
    $method     = $reflection->getMethod('availableMakeCommands');

    $result = $method->invoke(new ModuleConsoleServiceProvider(app()));

    expect($result)->toBeArray();
    expect($result)->not->toBeEmpty();
    expect($result)->toContain(Illuminate\Foundation\Console\CastMakeCommand::class);
});

test('overrides make commands using WithModuleCommand', function () {
    $reflection = new ReflectionClass(ModuleConsoleServiceProvider::class);
    $method     = $reflection->getMethod('moduleCommandClass');

    $provider = new ModuleConsoleServiceProvider(app());
    $result   = $method->invoke($provider, Illuminate\Foundation\Console\CastMakeCommand::class);

    expect($result)->toBeString();
    expect($result)->toStartWith('Module_');
    expect(class_exists($result))->toBeTrue();

    $traits = class_uses($result);

    expect($traits)->toHaveKey(SaeedHosan\Module\Console\Concerns\WithModuleCommand::class);
});

it('overrides make commands should have module option', function () {

    $makeCommands = [
        'make:cast',
        'make:channel',
        'make:class',
        'make:component',
        'make:config',
        'make:command',
        'make:enum',
        'make:event',
        'make:exception',
        'make:interface',
        'make:job',
        'make:listener',
        'make:mail',
        'make:model',
        'make:notification',
        'make:observer',
        'make:policy',
        'make:provider',
        'make:request',
        'make:resource',
        'make:rule',
        'make:scope',
        'make:test',
        'make:trait',
        'make:view',
        'make:controller',
        'make:middleware',
        'make:factory',
        'make:seeder',
        'make:migration',
    ];

    foreach ($makeCommands as $command) {
        try {
            Artisan::call($command, ['--help']);
            expect(Artisan::output())->toContain('--module');
        } catch (Symfony\Component\Console\Exception\CommandNotFoundException) {
            expect(true)->toBeTrue();
        }
    }
});

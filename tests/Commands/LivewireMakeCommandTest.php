<?php

declare(strict_types=1);

require_once __DIR__.'/../Support/LivewireStubs.php';

use Illuminate\Support\Facades\Artisan;
use Livewire\Finder\Finder;
use SaeedHosan\Module\Console\Commands\LivewireMakeCommand;
use SaeedHosan\Module\Console\ModuleConsoleServiceProvider;
use Tests\TestCase;

uses(TestCase::class);

it('defines a module-aware livewire make command wrapper', function (): void {
    expect(class_exists(LivewireMakeCommand::class))->toBeTrue();

    $reflection = new ReflectionClass(LivewireMakeCommand::class);

    expect($reflection->isSubclassOf(\Livewire\Features\SupportConsoleCommands\Commands\LivewireMakeCommand::class))->toBeTrue()
        ->and($reflection->hasMethod('handle'))->toBeTrue();
});

it('configures livewire paths for a module', function (): void {
    Artisan::call('make:module', ['name' => 'blog']);

    $module = module('blog');
    config()->set('livewire.component_namespaces', [
        'pages' => resource_path('views/pages'),
    ]);
    app()->instance('livewire.finder', new Finder());

    $command = app(LivewireMakeCommand::class);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('configureModuleLivewire');
    $method->setAccessible(true);
    $method->invoke($command, $module);

    expect(config('livewire.class_namespace'))->toBe('Modules\\Blog\\Livewire')
        ->and(config('livewire.class_path'))->toContain('modules/blog/app/Livewire')
        ->and(config('livewire.view_path'))->toContain('modules/blog/resources/views/livewire')
        ->and(config('livewire.component_locations'))->toBe([
            module('blog')->viewPath('livewire'),
        ])
        ->and(config('livewire.component_namespaces'))->toBe([
            'pages' => module('blog')->viewPath('livewire/pages'),
        ]);

    $finder = app('livewire.finder');

    expect($finder)->toBeInstanceOf(Finder::class)
        ->and($finder->locations)->toBe([
            module('blog')->viewPath('livewire'),
        ])
        ->and($finder->namespaces)->toHaveKey('pages')
        ->and($finder->namespaces['pages']['viewPath'])->toBe(module('blog')->viewPath('livewire/pages'))
        ->and($finder->namespaces['pages']['classNamespace'])->toBe('Modules\\Blog\\Livewire\\Pages');

    $finderProperty = $reflection->getProperty('finder');
    $finderProperty->setAccessible(true);

    expect($finderProperty->getValue($command))->toBe($finder);
});

it('registers the livewire make command provider hook only when livewire exists', function (): void {
    $provider = new ModuleConsoleServiceProvider(app());
    $reflection = new ReflectionClass($provider);

    expect($reflection->hasMethod('registerLivewireCommands'))->toBeTrue();
});

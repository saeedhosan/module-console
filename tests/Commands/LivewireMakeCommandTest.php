<?php

declare(strict_types=1);

require_once __DIR__.'/../Support/LivewireStubs.php';

use Illuminate\Support\Facades\Artisan;
use Livewire\Finder\Finder;
use Livewire\Features\SupportConsoleCommands\Commands\BaseLivewireMakeCommand;
use SaeedHosan\Module\Console\Commands\LivewireMakeCommand as ModuleLegacyLivewireMakeCommand;
use SaeedHosan\Module\Console\Commands\MakeCommand as ModuleMakeLivewireCommand;
use SaeedHosan\Module\Console\ModuleConsoleServiceProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    BaseLivewireMakeCommand::$observations = [];
    config()->set('livewire.class_namespace', 'App\\Livewire');
    config()->set('livewire.class_path', app_path('Livewire'));
    config()->set('livewire.view_path', resource_path('views/livewire'));
    config()->set('livewire.component_locations', [resource_path('views/components')]);
    config()->set('livewire.component_namespaces', [
        'pages' => resource_path('views/pages'),
    ]);

    app()->instance('livewire.finder', new Finder());
});

afterEach(function (): void {
    app()->forgetInstance('livewire.finder');
    BaseLivewireMakeCommand::$observations = [];

    $path = base_path('modules/blog');

    if (is_dir($path)) {
        app('files')->deleteDirectory($path);
    }
});

it('registers module-aware wrappers for both Livewire command classes', function (): void {
    app()->singleton(\Livewire\Features\SupportConsoleCommands\Commands\MakeCommand::class);
    app()->singleton(\Livewire\Features\SupportConsoleCommands\Commands\LivewireMakeCommand::class);

    $provider = new ModuleConsoleServiceProvider(app());
    $provider->boot();

    expect(app()->make(\Livewire\Features\SupportConsoleCommands\Commands\MakeCommand::class))
        ->toBeInstanceOf(ModuleMakeLivewireCommand::class)
        ->and(app()->make(\Livewire\Features\SupportConsoleCommands\Commands\LivewireMakeCommand::class))
        ->toBeInstanceOf(ModuleLegacyLivewireMakeCommand::class);
});

it('scopes make:livewire to a module and restores livewire state', function (): void {
    Artisan::call('make:module', ['name' => 'blog']);

    $command = app(ModuleMakeLivewireCommand::class);
    $command->setLaravel(app());

    $input = new ArrayInput([
        'name' => 'post-card',
        '--module' => 'blog',
    ]);

    $command->run($input, new BufferedOutput());

    expect(BaseLivewireMakeCommand::$observations)->toHaveCount(1);

    $observation = BaseLivewireMakeCommand::$observations[0];

    expect($observation['module'])->toBe('blog')
        ->and($observation['livewire.class_namespace'])->toBe('Modules\\Blog\\Livewire')
        ->and($observation['livewire.class_path'])->toContain('modules/blog/app/Livewire')
        ->and($observation['livewire.view_path'])->toContain('modules/blog/resources/views/livewire')
        ->and($observation['livewire.component_locations'])->toBe([
            module('blog')->viewPath('livewire'),
        ])
        ->and($observation['livewire.component_namespaces'])->toBe([
            'pages' => module('blog')->viewPath('livewire/pages'),
        ])
        ->and($observation['finder'])->toBeInstanceOf(Finder::class);

    expect($observation['finder']->locations)->toBe([
        module('blog')->viewPath('livewire'),
    ])
        ->and($observation['finder']->namespaces)->toHaveKey('pages')
        ->and($observation['finder']->namespaces['pages']['viewPath'])->toBe(module('blog')->viewPath('livewire/pages'))
        ->and($observation['finder']->namespaces['pages']['classNamespace'])->toBe('Modules\\Blog\\Livewire\\Pages');

    expect(config('livewire.class_namespace'))->toBe('App\\Livewire')
        ->and(config('livewire.class_path'))->toBe(app_path('Livewire'))
        ->and(config('livewire.view_path'))->toBe(resource_path('views/livewire'))
        ->and(config('livewire.component_locations'))->toBe([resource_path('views/components')])
        ->and(config('livewire.component_namespaces'))->toBe([
            'pages' => resource_path('views/pages'),
        ])
        ->and(app('livewire.finder'))->toBeInstanceOf(Finder::class);
});

it('scopes livewire:make to a module and restores livewire state', function (): void {
    Artisan::call('make:module', ['name' => 'blog']);

    $command = app(ModuleLegacyLivewireMakeCommand::class);
    $command->setLaravel(app());

    $input = new ArrayInput([
        'name' => 'post-card',
        '--module' => 'blog',
    ]);

    $command->run($input, new BufferedOutput());

    expect(BaseLivewireMakeCommand::$observations)->toHaveCount(1);

    $observation = BaseLivewireMakeCommand::$observations[0];

    expect($observation['module'])->toBe('blog')
        ->and($observation['livewire.class_namespace'])->toBe('Modules\\Blog\\Livewire')
        ->and($observation['livewire.class_path'])->toContain('modules/blog/app/Livewire')
        ->and($observation['livewire.view_path'])->toContain('modules/blog/resources/views/livewire')
        ->and($observation['livewire.component_locations'])->toBe([
            module('blog')->viewPath('livewire'),
        ])
        ->and($observation['livewire.component_namespaces'])->toBe([
            'pages' => module('blog')->viewPath('livewire/pages'),
        ])
        ->and($observation['finder'])->toBeInstanceOf(Finder::class);

    expect(config('livewire.class_namespace'))->toBe('App\\Livewire')
        ->and(config('livewire.class_path'))->toBe(app_path('Livewire'))
        ->and(config('livewire.view_path'))->toBe(resource_path('views/livewire'))
        ->and(config('livewire.component_locations'))->toBe([resource_path('views/components')])
        ->and(config('livewire.component_namespaces'))->toBe([
            'pages' => resource_path('views/pages'),
        ]);
});

<?php

declare(strict_types=1);

namespace Livewire\Finder;

class Finder
{
    /**
     * @var array<int, string>
     */
    public array $locations = [];

    /**
     * @var array<string, array{viewPath: string|null, classNamespace: string|null, classPath: string|null, classViewPath: string|null}>
     */
    public array $namespaces = [];

    public function addLocation($viewPath = null, $classNamespace = null): void
    {
        if ($viewPath !== null) {
            $this->locations[] = $viewPath;
        }
    }

    public function addNamespace($namespace, $viewPath = null, $classNamespace = null, $classPath = null, $classViewPath = null): void
    {
        $this->namespaces[$namespace] = [
            'viewPath' => $viewPath,
            'classNamespace' => $classNamespace,
            'classPath' => $classPath,
            'classViewPath' => $classViewPath,
        ];
    }
}

namespace Livewire\Features\SupportConsoleCommands\Commands;

use Illuminate\Console\Command;
use Livewire\Finder\Finder;

abstract class BaseLivewireMakeCommand extends Command
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public static array $observations = [];

    protected Finder $finder;

    public function __construct()
    {
        parent::__construct();

        $this->finder = app()->bound('livewire.finder') ? app('livewire.finder') : new Finder();
    }

    public function handle()
    {
        self::$observations[] = [
            'class' => static::class,
            'module' => $this->option('module'),
            'livewire.class_namespace' => config('livewire.class_namespace'),
            'livewire.class_path' => config('livewire.class_path'),
            'livewire.view_path' => config('livewire.view_path'),
            'livewire.component_locations' => config('livewire.component_locations'),
            'livewire.component_namespaces' => config('livewire.component_namespaces'),
            'finder' => app()->bound('livewire.finder') ? app('livewire.finder') : null,
        ];

        return 0;
    }
}

class MakeCommand extends BaseLivewireMakeCommand
{
    protected $signature = 'make:livewire {name}';
}

class LivewireMakeCommand extends BaseLivewireMakeCommand
{
    protected $signature = 'livewire:make {name}';
}

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

class LivewireMakeCommand extends Command
{
    protected $name = 'livewire:make';

    protected Finder $finder;

    public function __construct()
    {
        parent::__construct();

        $this->finder = app('livewire.finder');
    }

    public function handle()
    {
        return 0;
    }
}

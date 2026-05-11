<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console\Commands;

use Illuminate\Support\Str;
use Livewire\Finder\Finder;
use SaeedHosan\Module\Console\Concerns\WithModuleCommand;
use SaeedHosan\Module\Support\Module;

class LivewireMakeCommand extends \Livewire\Features\SupportConsoleCommands\Commands\LivewireMakeCommand
{
    use WithModuleCommand;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->option('module')) {
            return parent::handle();
        }

        $state = $this->snapshotLivewireState();

        try {
            $this->configureModuleLivewire($this->getModule());

            return parent::handle();
        } finally {
            $this->restoreLivewireState($state);
        }
    }

    /**
     * Store the current Livewire state so it can be restored after module generation.
     *
     * @return array{config: array<string, mixed>, finder: object|null}
     */
    private function snapshotLivewireState(): array
    {
        return [
            'config' => [
                'livewire.class_namespace' => config('livewire.class_namespace'),
                'livewire.class_path' => config('livewire.class_path'),
                'livewire.view_path' => config('livewire.view_path'),
                'livewire.component_locations' => config('livewire.component_locations'),
                'livewire.component_namespaces' => config('livewire.component_namespaces'),
            ],
            'finder' => app()->bound('livewire.finder') ? app('livewire.finder') : null,
        ];
    }

    /**
     * Restore the Livewire state after the command finishes.
     *
     * @param  array{config: array<string, mixed>, finder: object|null}  $state
     */
    private function restoreLivewireState(array $state): void
    {
        foreach ($state['config'] as $key => $value) {
            config()->set($key, $value);
        }

        if ($state['finder'] !== null) {
            app()->instance('livewire.finder', $state['finder']);

            $this->finder = $state['finder'];

            return;
        }

        app()->forgetInstance('livewire.finder');
    }

    private function configureModuleLivewire(Module $module): void
    {
        $moduleNamespace = $module->defaultNamespace();
        $moduleLivewireNamespace = $moduleNamespace.'\\Livewire';
        $moduleViewPath = $module->viewPath('livewire');
        $moduleClassPath = $module->appPath('Livewire');
        $moduleNamespaces = $this->moduleComponentNamespaces($module);

        config()->set('livewire.class_namespace', $moduleLivewireNamespace);
        config()->set('livewire.class_path', $moduleClassPath);
        config()->set('livewire.view_path', $moduleViewPath);
        config()->set('livewire.component_locations', [$moduleViewPath]);
        config()->set('livewire.component_namespaces', $moduleNamespaces);

        $finder = new Finder();

        $finder->addLocation($moduleViewPath, $moduleLivewireNamespace);

        foreach ($moduleNamespaces as $namespace => $viewPath) {
            $studlyNamespace = Str::studly($namespace);

            $finder->addNamespace(
                $namespace,
                $viewPath,
                $moduleLivewireNamespace.'\\'.$studlyNamespace,
                $module->appPath('Livewire', $studlyNamespace),
                $viewPath,
            );
        }

        app()->instance('livewire.finder', $finder);
        $this->finder = $finder;
    }

    /**
     * Build the module-scoped Livewire namespace map.
     *
     * @return array<string, string>
     */
    private function moduleComponentNamespaces(Module $module): array
    {
        $namespaces = config('livewire.component_namespaces', []);

        if (! is_array($namespaces)) {
            return [];
        }

        $moduleNamespaces = [];

        foreach (array_keys($namespaces) as $namespace) {
            $moduleNamespaces[$namespace] = $module->viewPath('livewire/'.$namespace);
        }

        return $moduleNamespaces;
    }
}

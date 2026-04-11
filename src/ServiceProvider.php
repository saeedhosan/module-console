<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use SaeedHosan\Module\Console\Commands\GenerateModuleSkeleton;
use SaeedHosan\Module\Console\Commands\ModuleListCommand;
use SaeedHosan\Module\Console\Concerns\WithModuleCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->alias('migration.creator', \Illuminate\Database\Migrations\MigrationCreator::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishStubs();
        $this->registerCommands();
        $this->registerAbout();
    }

    private function publishStubs(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'module-stubs');
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            GenerateModuleSkeleton::class,
            ModuleListCommand::class,
        ]);

        foreach ($this->availableMakeCommands() as $command) {
            $this->app->extend($command, function ($instance, $app) use ($command) {
                return $app->make($this->moduleCommandClass($command));
            });
        }
    }

    private function moduleCommandClass(string $command): string
    {
        $baseClass = '\\'.mb_ltrim($command, '\\');
        $alias     = 'Module_'.str_replace('\\', '_', $command);

        if (! class_exists($alias)) {
            $trait      = '\\'.mb_ltrim(WithModuleCommand::class, '\\');
            $definition = 'class '.$alias.' extends '.$baseClass.' { use '.$trait.'; }';

            eval($definition);
        }

        return $alias;
    }

    private function registerAbout(): void
    {
        if (! class_exists(InstalledVersions::class) || ! class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('Module', static fn () => [
            'Console version' => InstalledVersions::getPrettyVersion('saeedhosan/module-console'),
        ]);
    }

    private function availableMakeCommands(): array
    {
        return array_filter([
            \Illuminate\Foundation\Console\CastMakeCommand::class,
            \Illuminate\Foundation\Console\ChannelMakeCommand::class,
            \Illuminate\Foundation\Console\ClassMakeCommand::class,
            \Illuminate\Foundation\Console\ComponentMakeCommand::class,
            \Illuminate\Foundation\Console\ConfigMakeCommand::class,
            \Illuminate\Foundation\Console\ConsoleMakeCommand::class,
            \Illuminate\Foundation\Console\EnumMakeCommand::class,
            \Illuminate\Foundation\Console\EventMakeCommand::class,
            \Illuminate\Foundation\Console\ExceptionMakeCommand::class,
            \Illuminate\Foundation\Console\InterfaceMakeCommand::class,
            \Illuminate\Foundation\Console\JobMakeCommand::class,
            \Illuminate\Foundation\Console\JobMiddlewareMakeCommand::class,
            \Illuminate\Foundation\Console\ListenerMakeCommand::class,
            \Illuminate\Foundation\Console\MailMakeCommand::class,
            \Illuminate\Foundation\Console\ModelMakeCommand::class,
            \Illuminate\Foundation\Console\NotificationMakeCommand::class,
            \Illuminate\Foundation\Console\ObserverMakeCommand::class,
            \Illuminate\Foundation\Console\PolicyMakeCommand::class,
            \Illuminate\Foundation\Console\ProviderMakeCommand::class,
            \Illuminate\Foundation\Console\RequestMakeCommand::class,
            \Illuminate\Foundation\Console\ResourceMakeCommand::class,
            \Illuminate\Foundation\Console\RuleMakeCommand::class,
            \Illuminate\Foundation\Console\ScopeMakeCommand::class,
            \Illuminate\Foundation\Console\TestMakeCommand::class,
            \Illuminate\Foundation\Console\TraitMakeCommand::class,
            \Illuminate\Foundation\Console\ViewMakeCommand::class,
            \Illuminate\Routing\Console\ControllerMakeCommand::class,
            \Illuminate\Routing\Console\MiddlewareMakeCommand::class,
            \Illuminate\Database\Console\Factories\FactoryMakeCommand::class,
            \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class,
            \Illuminate\Database\Console\Seeds\SeederMakeCommand::class,

            // extend packages (no PHPStan error)
            'Laravel\Ai\Console\Commands\MakeAgentCommand',
            'Spatie\LaravelData\Commands\DataMakeCommand',
        ], 'class_exists');
    }
}

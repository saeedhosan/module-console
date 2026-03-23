<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console\Concerns;

use Illuminate\Support\Str;
use SaeedHosan\Module\Support\Module;
use SaeedHosan\Module\Support\ModuleManager;
use Symfony\Component\Console\Input\InputOption;

trait WithModuleCommand
{
    /**
     * Get the module name.
     */
    public function getModule(): Module
    {
        return app(ModuleManager::class)->module($this->option('module') ?? '');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if (! $this->option('module')) {
            return parent::rootNamespace();
        }

        return $this->getModule()->namespace();
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     */
    protected function getPath($name): string
    {
        if ($this instanceof \Illuminate\Foundation\Console\ViewMakeCommand) {
            return parent::getPath($name);
        }

        if (! $this->option('module')) {
            return parent::getPath($name);
        }

        $name = mb_ltrim(Str::replaceFirst($this->rootNamespace(), '', $name), '\\');

        return $this->getModule()->appPath().'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the first view directory path from the application configuration.
     *
     * @param  string  $path
     */
    protected function viewPath($path = ''): string
    {
        if (! $this->option('module')) {
            return parent::viewPath($path);
        }

        return $this->getModule()->viewPath($path);
    }

    /**
     * Get the destination test case path.
     *
     * @return string
     */
    protected function getTestPath()
    {
        if (! $this->option('module')) {
            return parent::getTestPath();
        }

        return $this->getModule()->basePath('tests', $this->testClassFullyQualifiedName().'test.php');
    }

    protected function configure()
    {
        parent::configure();

        $this->getDefinition()->addOption(
            new InputOption('--module', null, InputOption::VALUE_REQUIRED, 'Run inside an module')
        );
    }
}

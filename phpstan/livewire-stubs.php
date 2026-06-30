<?php

declare(strict_types=1);

namespace Livewire\Finder;

class Finder
{
    public function addLocation($viewPath = null, $classNamespace = null): void
    {
    }

    public function addNamespace($namespace, $viewPath = null, $classNamespace = null, $classPath = null, $classViewPath = null): void
    {
    }
}

namespace Livewire\Features\SupportConsoleCommands\Commands;

use Illuminate\Console\Command;
use Livewire\Finder\Finder;

abstract class MakeCommand extends Command
{
    protected Finder $finder;
}

abstract class LivewireMakeCommand extends MakeCommand
{
}

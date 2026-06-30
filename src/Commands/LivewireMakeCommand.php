<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console\Commands;

use SaeedHosan\Module\Console\Concerns\WithLivewireModuleCommand;

class LivewireMakeCommand extends \Livewire\Features\SupportConsoleCommands\Commands\LivewireMakeCommand
{
    use WithLivewireModuleCommand;
}

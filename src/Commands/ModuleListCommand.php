<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'module:list', description: 'Get list of the modules')]
class ModuleListCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(): void
    {

        $modules = module()->all()->map(function (\SaeedHosan\Module\Support\Module $module): array {
            return [
                'name'    => $module->name(),
                'version' => $module->version(),
                'enabled' => $module->active() ? 'Yes' : 'No',
                'path'    => Str::replaceFirst(
                    config('module.directory', 'modules').'/',
                    '',
                    $module->path()
                ),
            ];
        });

        $headers = is_array($first = $modules->first()) ? array_keys($first) : [];

        $headers = array_map('ucfirst', $headers);

        $this->table($headers, $modules->toArray());
    }
}

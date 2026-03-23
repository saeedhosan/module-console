<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'module:list', description: 'Get list of the modules')]
class ModuleListCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {

        $modules = module()->all()->map(function (\SaeedHosan\Module\Support\Module $module) {
            return [
                'name'    => $module->name(),
                'version' => $module->version(),
                'enabled' => $module->active() ? 'Yes' : 'No',
                'path'    => str_replace(base_path('/'), '', $module->appPath()),
            ];
        });

        $headers = is_array($first = $modules->first()) ? array_keys($first) : [];

        // capitalized
        $headers = array_map('ucfirst', $headers);

        $this->table($headers, $modules->toArray());
    }
}

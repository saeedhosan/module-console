<?php

declare(strict_types=1);

namespace SaeedHosan\Module\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'make:module', description: 'Create a new module skeleton')]
class GenerateModuleSkeleton extends Command implements PromptsForMissingInput
{
    /**
     * Create a new generator command instance.
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {

        $name = $this->getNameInput();

        if ($this->files->exists($this->getPath())) {

            $this->components->error(sprintf('module [%s] already exists.', $name));

            return false;
        }

        $stub = $this->getStub();

        $files = [];

        foreach ($stub as [$path, $contents]) {

            $file = str_replace('.stub', '', $path);

            $this->makeDirectory($file);

            $this->files->put($file, $contents);

            $files[] = [str_replace(base_path('/'), '', $file)];
        }

        $this->components->info(sprintf('module [%s] created successfully.', $name));

        $this->table(['File'], $files);
        $this->newLine();
        $this->info(sprintf('Please run: composer require %s', $this->getPackage()));

        return true;

    }

    /**
     * Get the desired class name from the input.
     */
    public function getNameInput()
    {
        $name = mb_trim($this->argument('name'));

        if (config('module.lowercase')) {
            return Str::lower($name);
        }

        return $name;
    }

    protected function getVendor(): string
    {
        return config('module.vendor', 'modules');
    }

    protected function getPackage(): string
    {
        return mb_strtolower($this->getVendor().'/'.$this->getNameInput());
    }

    protected function getPath()
    {
        $name = $this->getNameInput();

        return base_path(config('module.directory', 'modules').'/'.$name);
    }

    protected function getStubPath(): string
    {
        if (file_exists($customPath = $this->laravel->basePath('stubs/module'))) {
            return $customPath;
        }

        return __DIR__.'/../../stubs/module';
    }

    protected function getNamespace(): string
    {
        return config('module.namespace', 'Modules').'\\'.Str::studly($this->getNameInput());
    }

    protected function getComposerNamespace(): string
    {
        return str_replace('\\', '\\\\', $this->getNamespace());
    }

    protected function getStub(): array
    {
        if (! $this->files->exists($path = $this->getStubPath())) {
            throw new RuntimeException('Unable to locate the module stub.');
        }

        $files = Arr::map($this->files->allFiles($path, true), function ($file) {

            $path     = $this->replaceVars($file->getPathname());
            $contents = $this->replaceVars($file->getContents());

            $path     = Str::replaceFirst($this->getStubPath(), $this->getPath(), $path);
            $contents = $this->sortImports($contents);

            return [$path, $contents];
        });

        return $files;
    }

    /**
     * Get the console command arguments.
     *
     * @return array<int, mixed>
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module'],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, mixed>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => [
                'What should the module be named?',
                'E.g. billing',
            ],
        ];
    }

    protected function makeDirectory(string $path): string
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Alphabetically sorts the imports for the given stub.
     */
    protected function sortImports(string $stub): string
    {
        if (preg_match('/(?P<imports>(?:^use [^;{]+;$\n?)+)/m', $stub, $match)) {
            $imports = explode("\n", mb_trim($match['imports']));

            sort($imports);

            return str_replace(mb_trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }

    /**
     * Replace the variables in the stub with their respective values.
     */
    protected function replaceVars(string $stub): string
    {
        $templates = [
            '{{ name }}'          => $this->getNameInput(),
            '{{ slug }}'          => Str::slug($this->getNameInput()),
            '{{ title }}'         => Str::studly($this->getNameInput()),
            '{{ vendor }}'        => $this->getVendor(),
            '{{ version }}'       => '1.0',
            '{{ package }}'       => $this->getPackage(),
            '{{ namespace }}'     => $this->getNamespace(),
            '{{ rootNamespace }}' => str_replace('\\', '\\\\', $this->getNamespace()),
        ];

        $templates = array_merge($templates, Arr::mapWithKeys($templates, function ($value, $key) {
            return [str_replace(['{{ ', ' }}'], ['{{', '}}'], $key) => $value];
        }));

        $templates = array_merge($templates, Arr::mapWithKeys($templates, function ($value, $key) {
            return [str_replace(['{{ ', ' }}'], ['[', ']'], $key) => $value];
        }));

        return str_replace(array_keys($templates), array_values($templates), $stub);
    }
}

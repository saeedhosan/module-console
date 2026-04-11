<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use SaeedHosan\Module\Console\Commands\GenerateModuleSkeleton;
use Tests\TestCase;

uses(TestCase::class);

it('has correct command name', function () {
    $reflection = new ReflectionClass(GenerateModuleSkeleton::class);
    $attribute  = $reflection->getAttributes(Symfony\Component\Console\Attribute\AsCommand::class)[0]->newInstance();

    expect($attribute->name)->toBe('make:module');
});

it('has correct description', function () {
    $reflection = new ReflectionClass(GenerateModuleSkeleton::class);
    $attribute  = $reflection->getAttributes(Symfony\Component\Console\Attribute\AsCommand::class)[0]->newInstance();

    expect($attribute->description)->toBe('Create a new module skeleton');
});

it('is instance of command', function () {
    expect(GenerateModuleSkeleton::class)->toExtend(Command::class);
});

it('implements prompts for missing input', function () {
    expect(GenerateModuleSkeleton::class)->toImplement(Illuminate\Contracts\Console\PromptsForMissingInput::class);
});

it('handle method exists', function () {
    $reflection = new ReflectionClass(GenerateModuleSkeleton::class);

    expect($reflection->hasMethod('handle'))->toBeTrue();
});

it('getPath method exists', function () {
    $reflection = new ReflectionClass(GenerateModuleSkeleton::class);

    expect($reflection->hasMethod('getPath'))->toBeTrue();
});

it('getStubPath method exists', function () {
    $reflection = new ReflectionClass(GenerateModuleSkeleton::class);

    expect($reflection->hasMethod('getStubPath'))->toBeTrue();
});

it('generates a new module skeleton', function () {

    Artisan::call('make:module', ['name' => 'blog']);

    $module = module('blog');

    expect($module->name())->toBe('blog');
    expect($module->namespace())->toContain('Modules\Blog');
    expect($module->version())->toBe('1.0');
    expect($module->description())->toBeString();
    expect($module->basePath())->toContain('modules/blog');
    expect($module->path())->toContain('modules/blog');
    expect($module->exists())->toBeTrue();
});

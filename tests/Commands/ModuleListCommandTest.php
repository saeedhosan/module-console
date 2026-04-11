<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use SaeedHosan\Module\Console\Commands\ModuleListCommand;
use Tests\TestCase;

uses(TestCase::class);

it('has correct command name', function () {
    $reflection = new ReflectionClass(ModuleListCommand::class);
    $attribute  = $reflection->getAttributes(Symfony\Component\Console\Attribute\AsCommand::class)[0]->newInstance();

    expect($attribute->name)->toBe('module:list');
});

it('has correct description', function () {
    $reflection = new ReflectionClass(ModuleListCommand::class);
    $attribute  = $reflection->getAttributes(Symfony\Component\Console\Attribute\AsCommand::class)[0]->newInstance();

    expect($attribute->description)->toBe('Get list of the modules');
});

it('is instance of command', function () {
    expect(ModuleListCommand::class)->toExtend(Command::class);
});

it('handle method exists', function () {
    $reflection = new ReflectionClass(ModuleListCommand::class);

    expect($reflection->hasMethod('handle'))->toBeTrue();
});

it('list of module', function () {
    Artisan::call('module:list');
    expect(Artisan::output())->toBeString();
});

<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandRunner;
use Hephaestus\Tests\Fixtures\CreateUserCommand;

test('execute create user command', function () {
    $result = CommandRunner::for(CreateUserCommand::class)
        ->withArgs(['name' => 'John', 'email' => 'john@example.com'])
        ->run();

    $result->assertSuccessful()
        ->assertOutputEquals('User John with email john@example.com created!')
        ->assertExitCode(0);

    expect($result->isSuccessful())->toBeTrue()
        ->and($result->exitCode())->toBe(0)
        ->and($result->output())->toBeString()
        ->and($result->output())->toBe('User John with email john@example.com created!');
});

test('execute create user command with isAdmin option', function () {
    $result = CommandRunner::for(CreateUserCommand::class)
        ->withArgs(['name' => 'John', 'email' => 'john@example.com'])
        ->withOptions(['isAdmin' => true])
        ->run();

    $result->assertSuccessful()
        ->assertOutputContains('is an admin!')
        ->assertExitCode(0);

    expect($result->isSuccessful())->toBeTrue()
        ->and($result->exitCode())->toBe(0)
        ->and($result->output())->toBeString()
        ->and($result->output())->toContain('is an admin!');
});

test('can not execute create user command for missing argument name', function () {
    $result = CommandRunner::for(CreateUserCommand::class)
        ->run();

    $result->assertFailed()
        ->assertErrorOutputContains('(missing: "name, email")')
        ->assertExitCode(1);

    expect($result->isSuccessful())->toBeFalse()
        ->and($result->exitCode())->toBe(1)
        ->and($result->output())->toBeString()
        ->and($result->output())->toContain('(missing: "name, email")')
        ->and($result->output())->toBe('Not enough arguments (missing: "name, email").');
});

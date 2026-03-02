<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandRunner;
use Hephaestus\Tests\Fixtures\GreetCommand;

test('execute greet command', function () {
    $result = CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'John'])
        ->run();

    $result->assertSuccessful()
        ->assertOutputContains('John')
        ->assertOutputEquals('Hello, John!')
        ->assertExitCode(0);

    expect($result->isSuccessful())->toBeTrue()
        ->and($result->exitCode())->toBe(0)
        ->and($result->output())->toBeString()
        ->and($result->output())->toBe('Hello, John!');
});

test('execute greet command with yell option', function () {
    $result = CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'JOHN'])
        ->withOptions(['yell' => true])
        ->run();

    $result->assertSuccessful()
        ->assertOutputContains('Hello, JOHN!')
        ->assertOutputEquals('Hello, JOHN!')
        ->assertExitCode(0);

    expect($result->isSuccessful())->toBeTrue()
        ->and($result->exitCode())->toBe(0)
        ->and($result->output())->toBeString()
        ->and($result->output())->toBe('Hello, JOHN!');
});

test('can not execute greet command for missing argument name', function () {
    $result = CommandRunner::for(GreetCommand::class)
        ->run();

    $result->assertFailed()
        ->assertErrorOutputContains('(missing: "name")')
        ->assertExitCode(1);

    expect($result->isSuccessful())->toBeFalse()
        ->and($result->exitCode())->toBe(1)
        ->and($result->output())->toBeString()
        ->and($result->output())->toContain('(missing: "name")')
        ->and($result->output())->toBe('Not enough arguments (missing: "name").');
});

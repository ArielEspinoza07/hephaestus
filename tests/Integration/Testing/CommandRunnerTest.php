<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandRunner;
use Hephaestus\Tests\Fixtures\Bridge\ThrowingCommand;
use Hephaestus\Tests\Fixtures\GreetCommand;
use Hephaestus\Tests\Fixtures\Testing\AskNameCommand;
use Hephaestus\Tests\Fixtures\Testing\FailsWithCodeCommand;
use Hephaestus\Tests\Fixtures\Testing\Greeting;
use Hephaestus\Tests\Fixtures\Testing\GreetWithDependencyCommand;
use Hephaestus\Tests\Fixtures\Testing\TypeErrorCommand;
use Psr\Container\ContainerInterface;

test('exceptions are rendered to stderr and the command fails', function () {
    $result = CommandRunner::for(ThrowingCommand::class)->run();

    $result->assertFailed()
        ->assertExitCode(1)
        ->assertErrorOutputContains('deliberate failure');

    expect($result->output())->toBe('');
});

test('the exception code becomes the exit code', function () {
    CommandRunner::for(FailsWithCodeCommand::class)
        ->run()
        ->assertExitCode(3)
        ->assertErrorOutputContains('failed with a custom code');
});

test('errors propagate instead of being turned into a failed result', function () {
    expect(fn () => CommandRunner::for(TypeErrorCommand::class)->run())
        ->toThrow(TypeError::class, 'deliberate type error');
});

test('with* methods do not mutate the original runner', function () {
    $runner = CommandRunner::for(GreetCommand::class)->withArgs(['name' => 'John']);

    $runner->withArgs(['name' => 'Jane']);
    $runner->withOptions(['yell' => true]);

    $runner->run()->assertOutputEquals('Hello, John!');
});

test('withArgs merges and later values win', function () {
    CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'John'])
        ->withArgs(['name' => 'Jane'])
        ->run()
        ->assertSuccessful()
        ->assertOutputEquals('Hello, Jane!');
});

test('option keys without dashes are prefixed with "--"', function () {
    CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'John'])
        ->withOptions(['yell' => true])
        ->run()
        ->assertOutputEquals('Hello, JOHN!');
});

test('shortcut option keys are passed through unchanged', function () {
    CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'John'])
        ->withOptions(['-l' => true])
        ->run()
        ->assertOutputEquals('Hello, JOHN!');
});

test('withInputs answers interactive questions', function () {
    CommandRunner::for(AskNameCommand::class)
        ->withInputs(['Ariel'])
        ->run()
        ->assertSuccessful()
        ->assertOutputContains('Hello, Ariel!');
});

test('questions fall back to their default when no inputs are given', function () {
    CommandRunner::for(AskNameCommand::class)
        ->run()
        ->assertSuccessful()
        ->assertOutputContains('Hello, stranger!');
});

test('withContainer resolves the command through the container', function () {
    $command = new GreetWithDependencyCommand(new Greeting('Howdy'));

    $container = new class ($command) implements ContainerInterface {
        public function __construct(private readonly object $instance) {}

        public function get(string $id): object
        {
            return $this->instance;
        }

        public function has(string $id): bool
        {
            return true;
        }
    };

    CommandRunner::for(GreetWithDependencyCommand::class)
        ->withContainer($container)
        ->withArgs(['name' => 'John'])
        ->run()
        ->assertSuccessful()
        ->assertOutputEquals('Howdy, John!');
});

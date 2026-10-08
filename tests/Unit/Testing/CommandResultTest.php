<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandResult;
use PHPUnit\Framework\AssertionFailedError;

test('output and error output normalize line endings and trim', function () {
    $result = new CommandResult(exitCode: 0, output: "  Hello\r\nWorld\r\n", errorOutput: "\r\nOops\r\n");

    expect($result->output())->toBe("Hello\nWorld")
        ->and($result->errorOutput())->toBe('Oops');
});

test('isSuccessful is true only for exit code 0', function (int $exitCode, bool $expected) {
    $result = new CommandResult(exitCode: $exitCode, output: '', errorOutput: '');

    expect($result->isSuccessful())->toBe($expected);
})->with([
    'success' => [0, true],
    'failure' => [1, false],
    'invalid' => [2, false],
]);

test('assertions return the same result for chaining', function () {
    $result = new CommandResult(exitCode: 0, output: 'Hello', errorOutput: 'Warning');

    expect(
        $result->assertSuccessful()
            ->assertExitCode(0)
            ->assertOutputContains('Hell')
            ->assertOutputEquals('Hello')
            ->assertErrorOutputContains('Warn')
            ->assertErrorOutputEquals('Warning'),
    )->toBe($result);
});

test('assertFailed passes for any non-zero exit code', function () {
    $result = new CommandResult(exitCode: 3, output: '', errorOutput: '');

    expect($result->assertFailed())->toBe($result);
});

test('assertSuccessful fails with the actual exit code', function () {
    $result = new CommandResult(exitCode: 2, output: '', errorOutput: '');

    expect(fn () => $result->assertSuccessful())
        ->toThrow(AssertionFailedError::class, 'Expected exit code 0, got 2.');
});

test('assertFailed fails when the command exited with 0', function () {
    $result = new CommandResult(exitCode: 0, output: '', errorOutput: '');

    expect(fn () => $result->assertFailed())
        ->toThrow(AssertionFailedError::class, 'Expected command to fail, but it exited with code 0.');
});

test('assertExitCode fails on mismatch', function () {
    $result = new CommandResult(exitCode: 1, output: '', errorOutput: '');

    expect(fn () => $result->assertExitCode(2))
        ->toThrow(AssertionFailedError::class, 'Expected exit code 2, got 1.');
});

test('assertOutputContains fails when the needle is missing', function () {
    $result = new CommandResult(exitCode: 0, output: 'Hello', errorOutput: '');

    expect(fn () => $result->assertOutputContains('Bye'))
        ->toThrow(AssertionFailedError::class, 'Expected output to contain "Bye".');
});

test('assertOutputEquals fails on mismatch', function () {
    $result = new CommandResult(exitCode: 0, output: 'Hello', errorOutput: '');

    expect(fn () => $result->assertOutputEquals('Bye'))
        ->toThrow(AssertionFailedError::class, 'Output does not match expected value.');
});

test('assertErrorOutputContains checks only the error stream', function () {
    $result = new CommandResult(exitCode: 1, output: 'boom', errorOutput: '');

    expect(fn () => $result->assertErrorOutputContains('boom'))
        ->toThrow(AssertionFailedError::class, 'Expected error output to contain "boom".');
});

test('assertErrorOutputEquals fails on mismatch', function () {
    $result = new CommandResult(exitCode: 1, output: '', errorOutput: 'Oops');

    expect(fn () => $result->assertErrorOutputEquals('Boom'))
        ->toThrow(AssertionFailedError::class, 'Error output does not match expected value.');
});

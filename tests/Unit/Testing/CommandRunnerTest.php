<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandResult;
use Hephaestus\Testing\CommandRunner;
use Hephaestus\Tests\Fixtures\Bridge\ThrowingCommand;

test('runner converts any Throwable into a CommandResult with exitCode 1', function () {
    $result = CommandRunner::for(ThrowingCommand::class)->run();

    expect($result)->toBeInstanceOf(CommandResult::class)
        ->and($result->exitCode())->toBe(1)
        ->and($result->output())->toContain('deliberate failure');
});

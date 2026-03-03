<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandRunner;
use Hephaestus\Tests\Fixtures\CountCommand;

test('int and float arguments are automatically cast', function () {
    $result = CommandRunner::for(CountCommand::class)
        ->withArgs(['count' => '5', 'ratio' => '3.14'])
        ->run();

    $result->assertSuccessful()
        ->assertOutputEquals('count=5 ratio=3.14 verbose=false')
        ->assertExitCode(0);
});

test('bool option is cast correctly when set', function () {
    $result = CommandRunner::for(CountCommand::class)
        ->withArgs(['count' => '2', 'ratio' => '1.5'])
        ->withOptions(['verbose' => true])
        ->run();

    $result->assertSuccessful()
        ->assertOutputEquals('count=2 ratio=1.50 verbose=true')
        ->assertExitCode(0);
});

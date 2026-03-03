<?php

declare(strict_types=1);

use Hephaestus\Testing\CommandRunner;
use Hephaestus\Tests\Fixtures\DatabaseMigrateCommand;

test('it runs the database migration command', function () {
    $result = CommandRunner::for(DatabaseMigrateCommand::class)
        ->withArgs(['database' => 'sqlite', 'table' => 'users', 'columns' => ['name', 'email']])
        ->withOptions(['seed' => true])
        ->run();

    $result->assertSuccessful()
        ->assertExitCode(0)
        ->assertOutputContains('[OK] Migration completed successfully!');
});

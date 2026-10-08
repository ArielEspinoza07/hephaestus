<?php

declare(strict_types=1);

use Hephaestus\CliApp;
use Hephaestus\Tests\Fixtures\CliApp\CliAppTestCommand;
use Hephaestus\Tests\Fixtures\CliApp\Multi1\Multi1Command;
use Hephaestus\Tests\Fixtures\CliApp\Multi2\Multi2Command;

function cliAppCacheFile(): string
{
    return sys_get_temp_dir() . '/hephaestus_cliapp_cache_' . uniqid() . '.bin';
}

function fixtureDir(): string
{
    return realpath(__DIR__ . '/../Fixtures/CliApp');
}

function cliAppCacheFixtureDir(): string
{
    return realpath(__DIR__ . '/../Fixtures/CliApp/Cache');
}

afterEach(function () {
    if (isset($this->cachePath) && file_exists($this->cachePath)) {
        unlink($this->cachePath);
    }
});

test('registerCommands returns self for fluent chaining', function () {
    $app = CliApp::create('test-app');
    $result = $app->registerCommandsDirectory(fixtureDir());

    expect($app)->toBeInstanceOf(CliApp::class)
        ->and($result)->toBe($app);
});

test('registerCommands with cache path creates cache file', function () {
    $this->cachePath = cliAppCacheFile();
    $app = CliApp::create('test-app');

    $app->withCache($this->cachePath)
        ->registerCommandsDirectory(cliAppCacheFixtureDir());

    expect(file_exists($this->cachePath))->toBeTrue();
});

test('withCache returns self for fluent chaining', function () {
    $app = CliApp::create('test-app');
    $result = $app->withCache(cliAppCacheFile());

    expect($result)->toBe($app);
});

test('registerCommands without cache path does not throw', function () {
    $app = CliApp::create('test-app');
    $app->registerCommandsDirectory(fixtureDir());

    expect(true)->toBeTrue();
});

test('registerCommands accepts multiple directories', function () {
    $dir1 = realpath(__DIR__ . '/../Fixtures/CliApp/Multi1');
    $dir2 = realpath(__DIR__ . '/../Fixtures/CliApp/Multi2');

    $app = CliApp::create('test-app');
    $result = $app->registerCommandsDirectory([$dir1, $dir2]);

    expect($result)->toBe($app);
});

test('registerCommand returns self for fluent chaining', function () {
    $app = CliApp::create('test-app');
    $result = $app->registerCommand(CliAppTestCommand::class);

    expect($result)->toBe($app);
});

test('registerCommands accepts a list of command class names', function () {
    $app = CliApp::create('test-app');
    $result = $app->registerCommands([Multi1Command::class, Multi2Command::class]);

    expect($result)->toBe($app);
});

test('registerCommand throws when class does not extend Hephaestus Command', function () {
    $app = CliApp::create('test-app');

    $app->registerCommand(stdClass::class);
})->throws(RuntimeException::class);

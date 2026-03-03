<?php

declare(strict_types=1);

use Hephaestus\CliApp;

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
    $result = $app->registerCommands(fixtureDir());

    expect($app)->toBeInstanceOf(CliApp::class)
        ->and($result)->toBe($app);
});

test('registerCommands with cache path creates cache file', function () {
    $this->cachePath = cliAppCacheFile();
    $app = CliApp::create('test-app');

    $app->registerCommands(cliAppCacheFixtureDir(), $this->cachePath);

    expect(file_exists($this->cachePath))->toBeTrue();
});

test('registerCommands without cache path does not throw', function () {
    $app = CliApp::create('test-app');
    $app->registerCommands(fixtureDir());

    expect(true)->toBeTrue();
});

test('registerCommands accepts multiple directories', function () {
    $dir1 = realpath(__DIR__ . '/../Fixtures/CliApp/Multi1');
    $dir2 = realpath(__DIR__ . '/../Fixtures/CliApp/Multi2');

    $app = CliApp::create('test-app');
    $result = $app->registerCommands([$dir1, $dir2]);

    expect($result)->toBe($app);
});

<?php

declare(strict_types=1);

use Hephaestus\Cache\CommandCache;
use Hephaestus\CommandLoader;
use Hephaestus\Metadata\Support\CommandMetadata;
use Symfony\Component\Console\Command\Command;

function loaderCacheFile(): string
{
    return sys_get_temp_dir() . '/hephaestus_loader_cache_' . uniqid() . '.bin';
}

afterEach(function () {
    if (isset($this->cachePath) && file_exists($this->cachePath)) {
        unlink($this->cachePath);
    }
});

test('load returns Symfony commands without cache', function () {
    $loader = new CommandLoader();
    $dir = realpath(__DIR__ . '/../Fixtures/LoaderCommands/NoCache');

    $commands = $loader->load($dir);

    expect($commands)->toHaveCount(1)
        ->and($commands[0])->toBeInstanceOf(Command::class)
        ->and($commands[0]->getName())->toBe('app:no-cache');
});

test('load with cache writes cache file and returns commands', function () {
    $this->cachePath = loaderCacheFile();
    $cache = new CommandCache($this->cachePath);
    $loader = new CommandLoader($cache);
    $dir = realpath(__DIR__ . '/../Fixtures/LoaderCommands/WithCache');

    $commands = $loader->load($dir);

    expect($commands)->toHaveCount(1)
        ->and($commands[0]->getName())->toBe('app:with-cache')
        ->and(file_exists($this->cachePath))->toBeTrue();

    // Verify cache file contains correct metadata
    $file = realpath($dir . '/WithCacheCommand.php');
    $readCache = new CommandCache($this->cachePath);
    $cached = $readCache->get($file);

    expect($cached)->not->toBeNull()
        ->and($cached->signature)->toBe('app:with-cache');
});

test('load returns cached metadata when cache has a hit', function () {
    $this->cachePath = loaderCacheFile();
    $dir = realpath(__DIR__ . '/../Fixtures/LoaderCommands/CacheHit');
    $file = realpath($dir . '/CacheHitCommand.php');

    // Pre-populate the cache with different metadata than what the file declares
    $fakeMetadata = new CommandMetadata(
        target: 'Hephaestus\Tests\Fixtures\LoaderCommands\CacheHit\CacheHitCommand',
        signature: 'app:from-cache',
        description: 'Served from cache',
    );
    $writer = new CommandCache($this->cachePath);
    $writer->set($file, $fakeMetadata);
    $writer->flush();

    $loader = new CommandLoader(new CommandCache($this->cachePath));
    $commands = $loader->load($dir);

    expect($commands)->toHaveCount(1)
        ->and($commands[0]->getName())->toBe('app:from-cache');
});

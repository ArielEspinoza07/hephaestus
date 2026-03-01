<?php

declare(strict_types=1);

use Hephaestus\Cache\CommandCache;
use Hephaestus\Metadata\Support\CommandMetadata;

function makeMetadata(string $signature = 'app:test'): CommandMetadata
{
    return new CommandMetadata(
        target: 'SomeCommand',
        signature: $signature,
    );
}

function cacheFile(): string
{
    return sys_get_temp_dir() . '/hephaestus_test_cache_' . uniqid() . '.bin';
}

function realFile(): string
{
    return realpath(__DIR__ . '/../../Fixtures/TestCommand.php');
}

afterEach(function () {
    if (isset($this->cachePath) && file_exists($this->cachePath)) {
        unlink($this->cachePath);
    }
});

test('get returns null when cache file does not exist (cold cache)', function () {
    $this->cachePath = cacheFile();
    $cache = new CommandCache($this->cachePath);

    expect($cache->get(realFile()))->toBeNull();
});

test('get returns null for unknown file after set + flush on another file', function () {
    $this->cachePath = cacheFile();
    $cache = new CommandCache($this->cachePath);
    $file = realFile();

    $cache->set($file, makeMetadata());
    $cache->flush();

    expect($cache->get(sys_get_temp_dir() . '/does_not_exist.php'))->toBeNull();
});

test('get returns CommandMetadata on warm cache hit', function () {
    $this->cachePath = cacheFile();
    $file = realFile();
    $metadata = makeMetadata('app:cached');

    $writer = new CommandCache($this->cachePath);
    $writer->set($file, $metadata);
    $writer->flush();

    $reader = new CommandCache($this->cachePath);
    $result = $reader->get($file);

    expect($result)->toBeInstanceOf(CommandMetadata::class)
        ->and($result->signature)->toBe('app:cached');
});

test('flush persists data and second instance reads it back', function () {
    $this->cachePath = cacheFile();
    $file = realFile();
    $metadata = makeMetadata('app:persisted');

    $cache1 = new CommandCache($this->cachePath);
    $cache1->set($file, $metadata);
    $cache1->flush();

    expect(file_exists($this->cachePath))->toBeTrue();

    $cache2 = new CommandCache($this->cachePath);
    $result = $cache2->get($file);

    expect($result)->not->toBeNull()
        ->and($result->signature)->toBe('app:persisted');
});

test('flush is a no-op when cache is not dirty', function () {
    $this->cachePath = cacheFile();
    $cache = new CommandCache($this->cachePath);

    $cache->flush();

    expect(file_exists($this->cachePath))->toBeFalse();
});

test('get returns null and removes entry when file hash is stale', function () {
    $this->cachePath = cacheFile();
    $file = realFile();
    $metadata = makeMetadata('app:stale');

    // Manually build a cache file with a wrong hash
    $staleData = [
        $file => [
            'hash'     => 'wrong_hash',
            'metadata' => $metadata,
        ],
    ];
    file_put_contents($this->cachePath, serialize($staleData));

    $cache = new CommandCache($this->cachePath);

    expect($cache->get($file))->toBeNull();
});

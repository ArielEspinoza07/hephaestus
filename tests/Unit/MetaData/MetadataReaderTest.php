<?php

declare(strict_types=1);

use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Metadata\Support\CommandMetadata;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->commandClassName = TestCommand::class;
    $this->reader = new MetadataReader();
});

test('reads metadata for a class', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata)->toBeInstanceOf(CommandMetadata::class);
});

test('reads attributes from a Command class', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->signature)->toBeString()
        ->and($metadata->signature)->toBe('app:test')
        ->and($metadata->description)->toBeString()
        ->and($metadata->description)->toBe('Test command')
        ->and($metadata->help)->toBeString()
        ->and($metadata->help)->toBe('This is a test command')
        ->and($metadata->usages)->toBeArray()
        ->and($metadata->usages)->toHaveCount(1);
});

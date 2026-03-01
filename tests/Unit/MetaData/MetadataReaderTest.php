<?php

declare(strict_types=1);

use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Metadata\Support\CommandMetadata;
use Hephaestus\Tests\Fixtures\GreetCommand;
use Hephaestus\Tests\Fixtures\NoExtendCommand;
use Hephaestus\Tests\Fixtures\NoSignatureCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->commandClassName = TestCommand::class;
    $this->reader = new MetadataReader();
});

test('returns CommandMetadata instance', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata)->toBeInstanceOf(CommandMetadata::class);
});

test('returns CommandMetadata with signature', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->signature)->toBeString()
        ->and($metadata->signature)->toBe('app:test');
});

test('returns CommandMetadata with description', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->description)->toBeString()
        ->and($metadata->description)->toBe('Test command');
});

test('returns CommandMetadata with help', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->help)->toBeString()
        ->and($metadata->help)->toBe('This is a test command');
});

test('returns CommandMetadata with usages', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->usages)->toBeArray()
        ->and($metadata->usages)->toHaveCount(1);
});

test('returns CommandMetadata with hasInput', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->hasInput)->toBeBool()
        ->and($metadata->hasInput)->toBeFalse();
});

test('returns CommandMetadata with hasOutput', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->hasOutput)->toBeBool()
        ->and($metadata->hasOutput)->toBeFalse();
});

test('returns CommandMetadata with no parameters', function () {
    $metadata = $this->reader->read($this->commandClassName);

    expect($metadata->parameters)->toBeArray()
        ->and($metadata->parameters)->toBeEmpty();
});

test('returns CommandMetadata with parameters', function () {
    $metadata = $this->reader->read(GreetCommand::class);

    expect($metadata->parameters)->toBeArray()
        ->and($metadata->parameters)->toHaveCount(2);
});

test('throws exception when class does not extends from Command abstract class', function () {
    $this->reader->read(NoExtendCommand::class);
})->throws(RuntimeException::class, 'Command class "Hephaestus\Tests\Fixtures\NoExtendCommand" must extend "Hephaestus\Console\Command"');

test('throws exception when class does not have __invoke method', function () {
    $this->reader->read(NoSignatureCommand::class);
})->throws(RuntimeException::class, 'Command class "Hephaestus\Tests\Fixtures\NoSignatureCommand" must have an "__invoke" method');

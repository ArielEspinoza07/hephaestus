<?php

declare(strict_types=1);

use Hephaestus\Metadata\Resolver\SignatureAttributeResolver;
use Hephaestus\Tests\Fixtures\TestCommand;
use Hephaestus\Tests\Fixtures\NoSignatureCommand;

beforeEach(function () {
    $this->resolver = new SignatureAttributeResolver(
        extractor: new Hephaestus\Metadata\AttributeExtractor(),
    );
});

test('extracts signature from class attribute', function () {
    $signature = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($signature)->toBeString()
        ->and($signature)->toBe('app:test');
});

test('throws exception when no attribute is found', function () {
    $this->resolver->resolve(new ReflectionClass(NoSignatureCommand::class));
})->throws(RuntimeException::class);

<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\HelpAttributeResolver;
use Hephaestus\Tests\Fixtures\SignatureTestCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->resolver = new HelpAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('extracts help from class attribute', function () {
    $description = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($description)->toBeString()
        ->and($description)->toBe('This is a test command');
});

test('returns null when help is missing', function () {
    $description = $this->resolver->resolve(new ReflectionClass(SignatureTestCommand::class));

    expect($description)->toBeNull();
});

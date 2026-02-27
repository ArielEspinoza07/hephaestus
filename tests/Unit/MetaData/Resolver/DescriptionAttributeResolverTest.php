<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\DescriptionAttributeResolver;
use Hephaestus\Tests\Fixtures\SignatureTestCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->resolver = new DescriptionAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('extracts description from class attribute', function () {
    $description = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($description)->toBeString()
        ->and($description)->toBe('Test command');
});

test('returns null when description is missing', function () {
    $description = $this->resolver->resolve(new ReflectionClass(SignatureTestCommand::class));

    expect($description)->toBeNull();
});

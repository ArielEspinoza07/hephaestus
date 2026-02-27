<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\UsageAttributeResolver;
use Hephaestus\Tests\Fixtures\SignatureTestCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->resolver = new UsageAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('extracts usage from class attribute', function () {
    $description = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($description)->toBeArray()
        ->and($description)->toHaveCount(1);
});

test('returns empty array when usage is missing', function () {
    $description = $this->resolver->resolve(new ReflectionClass(SignatureTestCommand::class));

    expect($description)->toBeArray()
        ->and($description)->toBeEmpty();
});

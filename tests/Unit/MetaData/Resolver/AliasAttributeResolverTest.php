<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\AliasAttributeResolver;
use Hephaestus\Tests\Fixtures\SignatureTestCommand;
use Hephaestus\Tests\Fixtures\CreateUserCommand;

beforeEach(function () {
    $this->resolver = new AliasAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('extracts alias from class attribute', function () {
    $description = $this->resolver->resolve(new ReflectionClass(CreateUserCommand::class));

    expect($description)->toBeArray()
        ->and($description)->toHaveCount(3);
});

test('returns empty array when alias is missing', function () {
    $description = $this->resolver->resolve(new ReflectionClass(SignatureTestCommand::class));

    expect($description)->toBeArray()
        ->and($description)->toBeEmpty();
});

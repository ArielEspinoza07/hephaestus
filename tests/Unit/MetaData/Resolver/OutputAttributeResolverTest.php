<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\OutputAttributeResolver;
use Hephaestus\Tests\Fixtures\SignatureTestCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->resolver = new OutputAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('returns false when output is missing from class attribute', function () {
    $hasInput = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($hasInput)->toBeBool()
        ->and($hasInput)->toBeFalse();
});

test('returns true when has output from class attribute', function () {
    $hasInput = $this->resolver->resolve(new ReflectionClass(SignatureTestCommand::class));

    expect($hasInput)->toBeBool()
        ->and($hasInput)->toBeTrue();
});

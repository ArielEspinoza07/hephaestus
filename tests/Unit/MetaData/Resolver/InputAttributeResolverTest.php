<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\InputAttributeResolver;
use Hephaestus\Tests\Fixtures\SignatureTestCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->resolver = new InputAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('returns false when input is missing from class attribute', function () {
    $hasInput = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($hasInput)->toBeBool()
        ->and($hasInput)->toBeFalse();
});

test('returns true when has input from class attribute', function () {
    $hasInput = $this->resolver->resolve(new ReflectionClass(SignatureTestCommand::class));

    expect($hasInput)->toBeBool()
        ->and($hasInput)->toBeTrue();
});

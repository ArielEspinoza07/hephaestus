<?php

declare(strict_types=1);

use Hephaestus\Metadata\AttributeExtractor;
use Hephaestus\Metadata\Resolver\StyleAttributeResolver;
use Hephaestus\Tests\Fixtures\DatabaseMigrateCommand;
use Hephaestus\Tests\Fixtures\TestCommand;

beforeEach(function () {
    $this->resolver = new StyleAttributeResolver(
        extractor: new AttributeExtractor(),
    );
});

test('returns false when output is missing from class attribute', function () {
    $hasOutputStyle = $this->resolver->resolve(new ReflectionClass(TestCommand::class));

    expect($hasOutputStyle)->toBeBool()
        ->and($hasOutputStyle)->toBeFalse();
});

test('returns true when has output from class attribute', function () {
    $hasOutputStyle = $this->resolver->resolve(new ReflectionClass(DatabaseMigrateCommand::class));

    expect($hasOutputStyle)->toBeBool()
        ->and($hasOutputStyle)->toBeTrue();
});

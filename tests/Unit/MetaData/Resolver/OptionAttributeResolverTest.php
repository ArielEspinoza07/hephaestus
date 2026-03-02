<?php

declare(strict_types=1);

use Hephaestus\Attributes\Option;
use Hephaestus\Metadata\Resolver\OptionAttributeResolver;
use Hephaestus\Metadata\Support\OptionMetadata;
use Hephaestus\Tests\Fixtures\GreetCommand;

beforeEach(function () {
    $this->resolver = new OptionAttributeResolver();
});

test('extracts options from reflection parameter', function () {
    $method = new ReflectionMethod(GreetCommand::class, 'execute');
    $options = [];
    $parameters = array_filter(
        array: $method->getParameters(),
        callback: function (ReflectionParameter $parameter) {
            return array_filter(
                array: $parameter->getAttributes(),
                callback: fn (ReflectionAttribute $attribute) => $attribute->getName() === Option::class,
            );
        }
    );

    foreach ($parameters as $parameter) {
        $options[] = $this->resolver->resolve($parameter);
    }

    expect($options)->not->toBeEmpty()
        ->and($options)->toHaveCount(1);
});

test('returns OptionMetadata', function () {
    $method = new ReflectionMethod(GreetCommand::class, 'execute');
    $options = [];
    $parameters = array_filter(
        array: $method->getParameters(),
        callback: function (ReflectionParameter $parameter) {
            return array_filter(
                array: $parameter->getAttributes(),
                callback: fn (ReflectionAttribute $attribute) => $attribute->getName() === Option::class,
            );
        }
    );

    foreach ($parameters as $parameter) {
        $options[] = $this->resolver->resolve($parameter);
    }

    $parameter = array_last($options);

    expect($parameter)->toBeInstanceOf(OptionMetadata::class)
        ->and($parameter->name)->toBeString()
        ->and($parameter->name)->toBe('loud')
        ->and($parameter->type)->toBeString()
        ->and($parameter->type)->toBe('bool')
        ->and($parameter->description)->toBeString()
        ->and($parameter->description)->toBe('Whether to greet the user in a loud voice')
        ->and($parameter->acceptValue)->toBeBool()
        ->and($parameter->acceptValue)->toBeFalse()
        ->and($parameter->default)->toBeNull()
        ->and($parameter->isArray())->toBeBool()
        ->and($parameter->isArray())->toBeFalse()
        ->and($parameter->hasDefault())->toBeBool()
        ->and($parameter->hasDefault())->toBeFalse();
});

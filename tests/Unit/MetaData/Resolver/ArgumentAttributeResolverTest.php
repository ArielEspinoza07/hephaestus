<?php

declare(strict_types=1);

use Hephaestus\Attributes\Argument;
use Hephaestus\Metadata\Resolver\ArgumentAttributeResolver;
use Hephaestus\Metadata\Support\ArgumentMetadata;
use Hephaestus\Tests\Fixtures\GreetCommand;

beforeEach(function () {
    $this->resolver = new ArgumentAttributeResolver();
});

test('extracts arguments from reflection parameter', function () {
    $method = new ReflectionMethod(GreetCommand::class, 'execute');
    $arguments = [];
    $parameters = array_filter(
        array: $method->getParameters(),
        callback: function (ReflectionParameter $parameter) {
            return array_filter(
                array: $parameter->getAttributes(),
                callback: fn (ReflectionAttribute $attribute) => $attribute->getName() === Argument::class,
            );
        }
    );

    foreach ($parameters as $parameter) {
        $arguments[] = $this->resolver->resolve($parameter);
    }

    expect($arguments)->not->toBeEmpty()
        ->and($arguments)->toHaveCount(1);
});

test('returns ArgumentMetadata', function () {
    $method = new ReflectionMethod(GreetCommand::class, 'execute');
    $arguments = [];
    $parameters = array_filter(
        array: $method->getParameters(),
        callback: function (ReflectionParameter $parameter) {
            return array_filter(
                array: $parameter->getAttributes(),
                callback: fn (ReflectionAttribute $attribute) => $attribute->getName() === Argument::class,
            );
        }
    );

    foreach ($parameters as $parameter) {
        $arguments[] = $this->resolver->resolve($parameter);
    }

    $parameter = array_first($arguments);

    expect($parameter)->toBeInstanceOf(ArgumentMetadata::class)
        ->and($parameter->name)->toBeString()
        ->and($parameter->name)->toBe('name')
        ->and($parameter->type)->toBeString()
        ->and($parameter->type)->toBe('string')
        ->and($parameter->description)->toBeString()
        ->and($parameter->description)->toBe('The name of the user to greet')
        ->and($parameter->isRequired)->toBeBool()
        ->and($parameter->isRequired)->toBeTrue()
        ->and($parameter->default)->toBeNull()
        ->and($parameter->isArray())->toBeBool()
        ->and($parameter->isArray())->toBeFalse()
        ->and($parameter->hasDefault())->toBeBool()
        ->and($parameter->hasDefault())->toBeFalse();
});

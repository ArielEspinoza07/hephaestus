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
    $method = new ReflectionMethod(GreetCommand::class, '__invoke');
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
        ->and($arguments)->toHaveCount(1)
        ->and(array_first($arguments))->toBeInstanceOf(ArgumentMetadata::class);
});

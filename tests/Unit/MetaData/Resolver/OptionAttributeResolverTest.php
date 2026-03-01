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
    $method = new ReflectionMethod(GreetCommand::class, '__invoke');
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
        ->and($options)->toHaveCount(1)
        ->and(array_first($options))->toBeInstanceOf(OptionMetadata::class);
});

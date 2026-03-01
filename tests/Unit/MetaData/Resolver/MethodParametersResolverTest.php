<?php

declare(strict_types=1);

use Hephaestus\Metadata\Resolver\MethodParametersResolver;
use Hephaestus\Tests\Fixtures\GreetCommand;
use Hephaestus\Tests\Fixtures\InvalidCommand;

beforeEach(function () {
    $this->resolver = new MethodParametersResolver();
});

test('resolves method parameters', function () {
    $method = new ReflectionMethod(GreetCommand::class, '__invoke');

    $parameters = $this->resolver->resolve($method->getParameters());

    expect($parameters)->not->toBeEmpty()
        ->and($parameters)->toBeArray()
        ->and($parameters)->toHaveCount(2);
});

test('throws exception when parameter has multiple attributes', function () {
    $method = new ReflectionMethod(InvalidCommand::class, '__invoke');

    $this->resolver->resolve($method->getParameters());
})->throws(RuntimeException::class);

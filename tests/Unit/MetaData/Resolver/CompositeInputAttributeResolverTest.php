<?php

declare(strict_types=1);

use Hephaestus\Metadata\Resolver\ArgumentAttributeResolver;
use Hephaestus\Metadata\Resolver\CompositeInputAttributeResolver;
use Hephaestus\Metadata\Resolver\OptionAttributeResolver;
use Hephaestus\Tests\Fixtures\CreateUserCommand;
use Hephaestus\Tests\Fixtures\SendEmailCommand;

beforeEach(function () {
    $argumentAttributeResolver = new ArgumentAttributeResolver();
    $optionAttributeResolver = new OptionAttributeResolver();
    $this->resolver = new CompositeInputAttributeResolver(
        argumentResolver: $argumentAttributeResolver,
        optionResolver: $optionAttributeResolver,
    );
});

test('resolves composite input attribute from resolver', function () {
    $method = new ReflectionMethod(CreateUserCommand::class, '__invoke');
    $parameters = [];
    foreach ($method->getParameters() as $parameter) {
        $parameters[] = $this->resolver->resolve($parameter);
    }

    expect($parameters)->not->toBeEmpty()
        ->and($parameters)->toHaveCount(1);
});

test('throws exception when composite input attribute target does not have an constructor', function () {
    $method = new ReflectionMethod(SendEmailCommand::class, '__invoke');
    foreach ($method->getParameters() as $parameter) {
        $this->resolver->resolve($parameter);
    }
})->throws(RuntimeException::class);

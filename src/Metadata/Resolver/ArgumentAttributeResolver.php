<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Argument;
use Hephaestus\Metadata\Support\SimpleArgumentMetadata;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @template T of object
 */
final readonly class ArgumentAttributeResolver
{
    /**
     * @param ReflectionParameter $parameter
     * @return SimpleArgumentMetadata
     */
    public function resolve(ReflectionParameter $parameter): SimpleArgumentMetadata
    {
        /** @var ReflectionAttribute<T> $attribute */
        $attribute = array_filter(
            array: $parameter->getAttributes(),
            callback: fn (ReflectionAttribute $attribute) => $attribute->getName() === Argument::class,
        ) |> array_first(...);

        /** @var Argument $argument */
        $argument = $attribute->newInstance();
        /** @var ReflectionNamedType $parameterType */
        $parameterType = $parameter->getType();

        return new SimpleArgumentMetadata(
            name: $parameter->getName(),
            description: $argument->description,
            isRequired: $argument->required,
            isArray: mb_strtolower($parameterType->getName()) === 'array',
            defaultValue: $argument->default,
        );
    }
}

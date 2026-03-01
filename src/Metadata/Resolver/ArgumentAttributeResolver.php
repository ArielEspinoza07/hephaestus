<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Argument;
use Hephaestus\Metadata\Support\ArgumentMetadata;
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
     * @return ArgumentMetadata
     */
    public function resolve(ReflectionParameter $parameter): ArgumentMetadata
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

        $isRequired = $argument->required;
        if ($parameter->isDefaultValueAvailable()) {
            $isRequired = false;
        }

        return new ArgumentMetadata(
            name: $parameter->getName(),
            type: mb_strtolower($parameterType->getName()),
            description: $argument->description,
            isRequired: $isRequired,
            default: $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : $argument->default,
        );
    }
}

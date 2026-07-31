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
        $attribute = null;
        foreach ($parameter->getAttributes() as $candidate) {
            if ($candidate->getName() === Argument::class) {
                $attribute = $candidate;
                break;
            }
        }

        /** @var ReflectionAttribute<T> $attribute */
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
            type: strtolower($parameterType->getName()),
            description: $argument->description,
            isRequired: $isRequired,
            default: $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : $argument->default,
        );
    }
}

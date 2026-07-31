<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Option;
use Hephaestus\Metadata\Support\OptionMetadata;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @template T of object
 */
final class OptionAttributeResolver
{
    public function resolve(ReflectionParameter $parameter): OptionMetadata
    {
        $attribute = null;
        foreach ($parameter->getAttributes() as $candidate) {
            if ($candidate->getName() === Option::class) {
                $attribute = $candidate;
                break;
            }
        }

        /** @var ReflectionAttribute<T> $attribute */
        /** @var Option $option */
        $option = $attribute->newInstance();
        /** @var ReflectionNamedType $parameterType */
        $parameterType = $parameter->getType();

        $type = strtolower($parameterType->getName());

        $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : $option->default;
        if ($type === 'bool') {
            $default = null;
        }

        return new OptionMetadata(
            name: $parameter->getName(),
            type: $type,
            description: $option->description,
            acceptValue: $type === 'bool' ? false : $option->acceptValue,
            default: $default,
            shortcut: $option->shortcut,
        );
    }
}

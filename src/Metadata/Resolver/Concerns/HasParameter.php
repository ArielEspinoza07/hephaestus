<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver\Concerns;

use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionUnionType;
use RuntimeException;

trait HasParameter
{
    protected function hasParameterType(ReflectionParameter $parameter): void
    {
        if (! $parameter->hasType()) {
            throw new RuntimeException(
                message: sprintf(
                    'Missing type declaration on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                ),
            );
        }

        if ($parameter->getType() instanceof ReflectionUnionType
            || $parameter->getType() instanceof ReflectionIntersectionType) {
            throw new RuntimeException(
                message: sprintf(
                    'Union and intersection types are not supported on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                ),
            );
        }
    }

    protected function hasMultipleAttributes(ReflectionParameter $parameter): void
    {
        if (count($parameter->getAttributes()) > 1) {
            throw new RuntimeException(
                message: sprintf(
                    'Only one attribute is allowed on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName()
                )
            );
        }
    }
}

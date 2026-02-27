<?php

declare(strict_types=1);

namespace Hephaestus\Metadata;

use ReflectionAttribute;
use ReflectionClass;

/**
 * @template T of object
 */
final class AttributeExtractor
{
    /**
     * @param ReflectionClass<T> $class
     * @param class-string<T> $attributeClass
     * @return ReflectionAttribute<T>|null
     */
    public function extract(ReflectionClass $class, string $attributeClass): ?ReflectionAttribute
    {
        return array_first($class->getAttributes($attributeClass));
    }
}

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
     * @template TAttr of object
     * @param ReflectionClass<T> $class
     * @param class-string<TAttr> $attributeClass
     * @return ReflectionAttribute<TAttr>|null
     */
    public function extract(ReflectionClass $class, string $attributeClass): ?ReflectionAttribute
    {
        return array_first($class->getAttributes($attributeClass));
    }
}

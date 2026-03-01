<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Alias;
use Hephaestus\Metadata\AttributeExtractor;
use ReflectionClass;

/**
 * @template T of object
 */
final readonly class AliasAttributeResolver
{
    /**
     * @param AttributeExtractor<T> $extractor
     */
    public function __construct(
        private AttributeExtractor $extractor,
    ) {}

    /**
     * @param ReflectionClass<T> $class
     * @return list<string>
     */
    public function resolve(ReflectionClass $class): array
    {
        $attribute = $this->extractor->extract($class, Alias::class);
        if ($attribute === null) {

            return [];
        }

        $alias = $attribute->newInstance();

        return explode('|', $alias->value);
    }
}

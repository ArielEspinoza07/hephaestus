<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Usage;
use Hephaestus\Metadata\AttributeExtractor;
use ReflectionClass;

/**
 * @template T of object
 */
final readonly class UsageAttributeResolver
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
        $attribute = $this->extractor->extract($class, Usage::class);
        if ($attribute === null) {

            return [];
        }

        $signature = $attribute->newInstance();

        return $signature->value;
    }
}

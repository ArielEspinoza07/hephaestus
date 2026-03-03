<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Style;
use Hephaestus\Metadata\AttributeExtractor;
use ReflectionClass;

/**
 * @template T of object
 */
final readonly class StyleAttributeResolver
{
    /**
     * @param AttributeExtractor<T> $extractor
     */
    public function __construct(
        private AttributeExtractor $extractor,
    ) {}

    /**
     * @param ReflectionClass<T> $class
     */
    public function resolve(ReflectionClass $class): bool
    {
        return (bool)$this->extractor->extract($class, Style::class);
    }
}

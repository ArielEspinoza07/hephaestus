<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Help;
use Hephaestus\Metadata\AttributeExtractor;
use ReflectionClass;

/**
 * @template T of object
 */
final readonly class HelpAttributeResolver
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
    public function resolve(ReflectionClass $class): ?string
    {
        $attribute = $this->extractor->extract($class, Help::class);
        if ($attribute === null) {

            return null;
        }

        $signature = $attribute->newInstance();

        return $signature->value;
    }
}

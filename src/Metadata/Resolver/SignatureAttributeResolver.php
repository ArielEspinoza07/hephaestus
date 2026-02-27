<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Signature;
use Hephaestus\Metadata\AttributeExtractor;
use ReflectionClass;
use RuntimeException;

/**
 * @template T of object
 */
final readonly class SignatureAttributeResolver
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
    public function resolve(ReflectionClass $class): string
    {
        $attribute = $this->extractor->extract($class, Signature::class);
        if ($attribute === null) {
            throw new RuntimeException(
                message: sprintf('No #[Signature] attribute found, in class: %s', $class->getName()),
            );
        }

        $signature = $attribute->newInstance();

        return $signature->value;
    }
}

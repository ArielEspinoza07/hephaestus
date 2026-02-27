<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Metadata\Support\ArgumentMetadataContract;
use Hephaestus\Metadata\Support\CompositeInputMetadata;
use RuntimeException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @template T of object
 */
final readonly class CompositeInputAttributeResolver
{
    /**
     * @param ArgumentAttributeResolver<T> $argumentAttributeResolver
     */
    public function __construct(
        private ArgumentAttributeResolver $argumentAttributeResolver,
    ) {}

    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter): CompositeInputMetadata
    {
        /** @var ReflectionNamedType $parameterType */
        $parameterType = $parameter->getType();

        return new CompositeInputMetadata(
            target: $parameterType->getName(),
            properties: $this->getProperties($parameterType->getName()),
        );
    }

    /**
     * @param class-string $targetClass
     * @return list<ArgumentMetadataContract>
     * @throws ReflectionException
     */
    private function getProperties(string $targetClass): array
    {
        $reflectionClass = new ReflectionClass($targetClass);
        if (! $reflectionClass->getConstructor()) {
            throw new RuntimeException(
                message: sprintf('Class %s does not have a constructor', $targetClass),
            );
        }

        $properties = [];
        foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
            $properties[] = $this->argumentAttributeResolver->resolve($parameter);
        }

        return $properties;
    }
}

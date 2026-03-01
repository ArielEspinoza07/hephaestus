<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Option;
use Hephaestus\Metadata\Resolver\Concerns\HasParameter;
use Hephaestus\Metadata\Support\InputMetadataContract;
use Hephaestus\Metadata\Support\CompositeInputMetadata;
use ReflectionAttribute;
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
    use HasParameter;

    /**
     * @param ArgumentAttributeResolver<T> $argumentResolver
     * @param OptionAttributeResolver<T> $optionResolver
     */
    public function __construct(
        private ArgumentAttributeResolver $argumentResolver,
        private OptionAttributeResolver $optionResolver,
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
     * @return list<InputMetadataContract>
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

        $parameters = [];
        foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
            $this->hasMultipleAttributes($parameter);
            $this->hasParameterType($parameter);

            /** @var ReflectionNamedType $parameterType */
            $parameterType = $parameter->getType();
            $attribute = array_first($parameter->getAttributes());
            $this->checkIfParameterTypeIsAllowedByAttribute($parameter, $parameterType, $attribute);
            $parameters[] = match ($attribute->getName()) {
                Argument::class => $this->argumentResolver->resolve($parameter),
                Option::class => $this->optionResolver->resolve($parameter),
                default => throw new RuntimeException(
                    message: sprintf(
                        'Attribute %s is not supported.',
                        $attribute->getName(),
                    )
                ),
            };
        }

        return $parameters;
    }

    /**
     * @param ReflectionAttribute<T> $attribute
     */
    private function checkIfParameterTypeIsAllowedByAttribute(
        ReflectionParameter $parameter,
        ReflectionNamedType $parameterType,
        ReflectionAttribute $attribute,
    ): void {
        if ($attribute->getName() === Argument::class && class_exists($parameterType->getName())) {
            throw new RuntimeException(
                message: sprintf(
                    'Incorrect type declaration on parameter %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                ),
            );
        }
        if ($attribute->getName() === Option::class && class_exists($parameterType->getName())) {
            throw new RuntimeException(
                message: sprintf(
                    'Incorrect type declaration on parameter %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                ),
            );
        }
    }
}

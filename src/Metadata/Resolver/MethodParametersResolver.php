<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Option;
use Hephaestus\Metadata\Resolver\Concerns\HasParameter;
use Hephaestus\Metadata\Support\InputMetadataContract;
use ReflectionAttribute;
use ReflectionException;
use RuntimeException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @template T of object
 */
final readonly class MethodParametersResolver
{
    use HasParameter;

    /**
     * @var ArgumentAttributeResolver<T> $argumentResolver
     */
    private ArgumentAttributeResolver $argumentResolver;

    /**
     * @var CompositeInputAttributeResolver<T> $compositeInputResolver
     */
    private CompositeInputAttributeResolver $compositeInputResolver;

    /**
     * @var OptionAttributeResolver<T> $optionResolver
     */
    private OptionAttributeResolver $optionResolver;

    public function __construct()
    {
        $this->argumentResolver = new ArgumentAttributeResolver();
        $this->optionResolver = new OptionAttributeResolver();
        $this->compositeInputResolver = new CompositeInputAttributeResolver(
            argumentResolver: $this->argumentResolver,
            optionResolver: $this->optionResolver,
        );
    }

    /**
     * @param list<ReflectionParameter> $methodParameters
     * @return list<InputMetadataContract>
     * @throws ReflectionException
     */
    public function resolve(array $methodParameters): array
    {
        $parameters = [];
        foreach ($methodParameters as $parameter) {
            $this->hasMultipleAttributes($parameter);
            $this->hasParameterType($parameter);

            /** @var ReflectionNamedType $parameterType */
            $parameterType = $parameter->getType();
            /** @var ReflectionAttribute<object> $attribute */
            $attribute = array_first($parameter->getAttributes());
            $this->checkIfParameterTypeIsAllowedByAttribute($parameter, $parameterType, $attribute);
            $parameters[] = match ($attribute->getName()) {
                Argument::class => $this->argumentResolver->resolve($parameter),
                Option::class => $this->optionResolver->resolve($parameter),
                CompositeInput::class => $this->compositeInputResolver->resolve($parameter),
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
     * @param ReflectionAttribute<object> $attribute
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
                    $parameter->getDeclaringClass()?->getName() ?? 'unknown',
                ),
            );
        }
        if ($attribute->getName() === Option::class && class_exists($parameterType->getName())) {
            throw new RuntimeException(
                message: sprintf(
                    'Incorrect type declaration on parameter %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()?->getName() ?? 'unknown',
                ),
            );
        }
        if ($attribute->getName() === CompositeInput::class && ! class_exists($parameterType->getName())) {
            throw new RuntimeException(
                message: sprintf(
                    'Incorrect type declaration on parameter %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()?->getName() ?? 'unknown',
                ),
            );
        }
    }
}

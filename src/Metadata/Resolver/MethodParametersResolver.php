<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Resolver;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Metadata\Support\ArgumentMetadataContract;
use ReflectionAttribute;
use ReflectionIntersectionType;
use ReflectionUnionType;
use RuntimeException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @template T of object
 */
final readonly class MethodParametersResolver
{
    /**
     * @var ArgumentAttributeResolver<T> $argumentResolver
     */
    private ArgumentAttributeResolver $argumentResolver;

    /**
     * @var CompositeInputAttributeResolver<T> $compositeInputResolver
     */
    private CompositeInputAttributeResolver $compositeInputResolver;

    public function __construct()
    {
        $this->argumentResolver = new ArgumentAttributeResolver();
        $this->compositeInputResolver = new CompositeInputAttributeResolver($this->argumentResolver);
    }

    /**
     * @param list<ReflectionParameter> $methodParameters
     * @return list<ArgumentMetadataContract>
     */
    public function resolve(array $methodParameters): array
    {
        $parameters = [];
        foreach ($methodParameters as $parameter) {
            $argumentAttribute = array_first($parameter->getAttributes(Argument::class));
            $compositeInputAttribute = array_first($parameter->getAttributes(CompositeInput::class));
            $this->checkParameterType($parameter);

            /** @var ReflectionNamedType $parameterType */
            $parameterType = $parameter->getType();

            $this->checkIfArgumentAttributeIsSetOnTypeClassParameter($parameterType, $parameter, $argumentAttribute);
            $this->checkIfCompositeInputAttributeIsSetOnNotTypeClassParameter($parameterType, $parameter, $compositeInputAttribute);
            if ($argumentAttribute && ! $compositeInputAttribute && ! class_exists($parameterType->getName())) {
                $parameters[] = $this->argumentResolver->resolve($parameter);
            }
            if (! $argumentAttribute && $compositeInputAttribute && class_exists($parameterType->getName())) {
                $parameters[] = $this->compositeInputResolver->resolve($parameter);
            }
        }

        return $parameters;
    }

    /**
     * @param ReflectionAttribute<T>|null $compositeInputAttribute
     * @param ReflectionNamedType $parameterType
     * @param ReflectionParameter $parameter
     * @return void
     */
    public function checkIfCompositeInputAttributeIsSetOnNotTypeClassParameter(
        ReflectionNamedType $parameterType,
        ReflectionParameter $parameter,
        ?ReflectionAttribute $compositeInputAttribute,
    ): void {
        if ($compositeInputAttribute && !class_exists($parameterType->getName())) {
            throw new RuntimeException(
                message: sprintf(
                    'Incorrect attribute #[CompositeInput] on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName()
                )
            );
        }
    }

    /**
     * @param ReflectionParameter $parameter
     * @return void
     */
    private function checkParameterType(ReflectionParameter $parameter): void
    {
        if (! $parameter->hasType()) {
            throw new RuntimeException(
                message: sprintf(
                    'Missing type declaration on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                ),
            );
        }

        if ($parameter->getType() instanceof ReflectionUnionType
            || $parameter->getType() instanceof ReflectionIntersectionType) {
            throw new RuntimeException(
                message: sprintf(
                    'Union and intersection types are not supported on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName(),
                ),
            );
        }
    }

    /**
     * @param ReflectionNamedType $parameterType
     * @param ReflectionParameter $parameter
     * @param ReflectionAttribute<T>|null $argumentAttribute
     * @return void
     */
    private function checkIfArgumentAttributeIsSetOnTypeClassParameter(
        ReflectionNamedType $parameterType,
        ReflectionParameter $parameter,
        ?ReflectionAttribute $argumentAttribute,
    ): void {
        if ($argumentAttribute && class_exists($parameterType->getName())) {
            throw new RuntimeException(
                message: sprintf(
                    'Incorrect attribute #[Argument] on property %s in class %s.',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()->getName()
                )
            );
        }
    }
}

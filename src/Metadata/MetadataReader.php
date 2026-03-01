<?php

declare(strict_types=1);

namespace Hephaestus\Metadata;

use Hephaestus\Console\Command;
use Hephaestus\Metadata\Resolver\AliasAttributeResolver;
use Hephaestus\Metadata\Resolver\DescriptionAttributeResolver;
use Hephaestus\Metadata\Resolver\HelpAttributeResolver;
use Hephaestus\Metadata\Resolver\InputAttributeResolver;
use Hephaestus\Metadata\Resolver\MethodParametersResolver;
use Hephaestus\Metadata\Resolver\OutputAttributeResolver;
use Hephaestus\Metadata\Resolver\SignatureAttributeResolver;
use Hephaestus\Metadata\Resolver\UsageAttributeResolver;
use Hephaestus\Metadata\Support\InputMetadataContract;
use Hephaestus\Metadata\Support\CommandMetadata;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * @template T of object
 */
final readonly class MetadataReader
{
    private const string DEFAULT_COMMAND_METHOD = 'execute';
    /**
     * @var SignatureAttributeResolver<T> $signatureResolver
     */
    private SignatureAttributeResolver $signatureResolver;

    /**
     * @var DescriptionAttributeResolver<T> $descriptionResolver
     */
    private DescriptionAttributeResolver $descriptionResolver;

    /**
     * @var HelpAttributeResolver<T> $helpResolver
     */
    private HelpAttributeResolver $helpResolver;

    /**
     * @var UsageAttributeResolver<T> $usageResolver
     */
    private UsageAttributeResolver $usageResolver;

    /**
     * @var AliasAttributeResolver<T> $aliasResolver
     */
    private AliasAttributeResolver $aliasResolver;

    /**
     * @var InputAttributeResolver<T> $inputResolver
     */
    private InputAttributeResolver $inputResolver;

    /**
     * @var OutputAttributeResolver<T> $outputResolver
     */
    private OutputAttributeResolver $outputResolver;

    /**
     * @var MethodParametersResolver<T> $methodParametersResolver
     */
    private MethodParametersResolver $methodParametersResolver;

    public function __construct()
    {
        /** @var AttributeExtractor<T> $attributeExtractor */
        $attributeExtractor = new AttributeExtractor();
        $this->signatureResolver = new SignatureAttributeResolver($attributeExtractor);
        $this->descriptionResolver = new DescriptionAttributeResolver($attributeExtractor);
        $this->helpResolver = new HelpAttributeResolver($attributeExtractor);
        $this->usageResolver = new UsageAttributeResolver($attributeExtractor);
        $this->aliasResolver = new AliasAttributeResolver($attributeExtractor);
        $this->inputResolver = new InputAttributeResolver($attributeExtractor);
        $this->outputResolver = new OutputAttributeResolver($attributeExtractor);
        $this->methodParametersResolver = new MethodParametersResolver();
    }

    /**
     * @param class-string<T> $className
     *
     * @throws ReflectionException
     */
    public function read(string $className): CommandMetadata
    {
        /** @var ReflectionClass<T> $reflectionClass */
        $reflectionClass = new ReflectionClass($className);
        $this->checkParentClass($reflectionClass);
        $this->checkExecuteMethod($reflectionClass);

        $parameters = $this->getMethodParameters($reflectionClass);

        return new CommandMetadata(
            target: $className,
            signature: $this->signatureResolver->resolve($reflectionClass),
            description: $this->descriptionResolver->resolve($reflectionClass),
            help: $this->helpResolver->resolve($reflectionClass),
            hasInput: count($parameters) > 0 ? false : $this->inputResolver->resolve($reflectionClass),
            hasOutput: $this->outputResolver->resolve($reflectionClass),
            usages: $this->usageResolver->resolve($reflectionClass),
            aliases: $this->aliasResolver->resolve($reflectionClass),
            parameters: $parameters,
        );
    }

    /**
     * @param ReflectionClass<T> $class
     */
    private function checkParentClass(ReflectionClass $class): void
    {
        if ($class->getParentClass() === false || $class->getParentClass()->getName() !== Command::class) {
            throw new RuntimeException(
                message: sprintf(
                    'Command class "%s" must extend "%s"',
                    $class->getName(),
                    Command::class,
                ),
            );
        }
    }

    /**
     * @param ReflectionClass<T> $class
     */
    private function checkExecuteMethod(ReflectionClass $class): void
    {
        $method = array_find(
            array: $class->getMethods(),
            callback: fn (ReflectionMethod $method) => $method->getName() === self::DEFAULT_COMMAND_METHOD,
        );
        if ($method === null) {
            throw new RuntimeException(
                message: sprintf(
                    'Command class "%s" must have an "%s" method',
                    $class->getName(),
                    self::DEFAULT_COMMAND_METHOD,
                ),
            );
        }
    }

    /**
     * @param ReflectionClass<T> $class
     * @return list<InputMetadataContract>
     * @throws ReflectionException
     */
    private function getMethodParameters(ReflectionClass $class): array
    {
        $parameters = $class->getMethod(self::DEFAULT_COMMAND_METHOD)->getParameters();
        if (count($parameters) === 0) {
            return [];
        }

        return $this->methodParametersResolver
            ->resolve(
                methodParameters: $parameters,
            );
    }
}

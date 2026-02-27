<?php

declare(strict_types=1);

namespace Hephaestus\Metadata;

use Hephaestus\Metadata\Resolver\DescriptionAttributeResolver;
use Hephaestus\Metadata\Resolver\HelpAttributeResolver;
use Hephaestus\Metadata\Resolver\InputAttributeResolver;
use Hephaestus\Metadata\Resolver\OutputAttributeResolver;
use Hephaestus\Metadata\Resolver\SignatureAttributeResolver;
use Hephaestus\Metadata\Resolver\UsageAttributeResolver;
use Hephaestus\Metadata\Support\CommandMetadata;
use ReflectionClass;
use ReflectionException;

/**
 * @template T of object
 */
final readonly class MetadataReader
{
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
     * @var InputAttributeResolver<T> $inputResolver
     */
    private InputAttributeResolver $inputResolver;

    /**
     * @var OutputAttributeResolver<T> $outputResolver
     */
    private OutputAttributeResolver $outputResolver;

    public function __construct()
    {
        /** @var AttributeExtractor<T> $attributeExtractor */
        $attributeExtractor = new AttributeExtractor();
        $this->signatureResolver = new SignatureAttributeResolver($attributeExtractor);
        $this->descriptionResolver = new DescriptionAttributeResolver($attributeExtractor);
        $this->helpResolver = new HelpAttributeResolver($attributeExtractor);
        $this->usageResolver = new UsageAttributeResolver($attributeExtractor);
        $this->inputResolver = new InputAttributeResolver($attributeExtractor);
        $this->outputResolver = new OutputAttributeResolver($attributeExtractor);
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

        return new CommandMetadata(
            signature: $this->signatureResolver->resolve($reflectionClass),
            description: $this->descriptionResolver->resolve($reflectionClass),
            help: $this->helpResolver->resolve($reflectionClass),
            hasInput: $this->inputResolver->resolve($reflectionClass),
            hasOutput: $this->outputResolver->resolve($reflectionClass),
            usages: $this->usageResolver->resolve($reflectionClass),
        );
    }
}

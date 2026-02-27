<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Support;

final readonly class CompositeInputMetadata implements ArgumentMetadataContract
{
    /**
     * @param list<ArgumentMetadataContract> $properties
     */
    public function __construct(
        public string $target,
        public array $properties,
    ) {}
}

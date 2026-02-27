<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Support;

final readonly class SimpleArgumentMetadata implements ArgumentMetadataContract
{
    /**
     * @param string|int|float|bool|array<int|string, mixed>|null $defaultValue
     */
    public function __construct(
        public string $name,
        public string $description = '',
        public bool $isRequired = true,
        public bool $isArray = false,
        public string|int|float|bool|array|null $defaultValue = null,
    ) {}
}

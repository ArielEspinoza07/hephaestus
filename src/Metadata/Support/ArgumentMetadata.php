<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Support;

final readonly class ArgumentMetadata implements InputMetadataContract
{
    /**
     * @param string|int|float|bool|array<int|string, mixed>|null $default
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $description = '',
        public bool $isRequired = true,
        public string|int|float|bool|array|null $default = null,
    ) {}

    public function isArray(): bool
    {
        return $this->type === 'array';
    }

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }
}

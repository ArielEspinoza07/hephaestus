<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Support;

final readonly class OptionMetadata
{
    /**
     * @param string|int|float|bool|array<int|string, mixed>|null $default
     * @param string|list<string>|null $shortcut
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $description = '',
        public bool $acceptValue = false,
        public string|int|float|bool|array|null $default = null,
        public array|string|null $shortcut = null,
    ) {}

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }
}

<?php

declare(strict_types=1);

namespace Hephaestus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Argument
{
    /**
     * @param string|int|float|bool|array<int|string, mixed>|null $default
     */
    public function __construct(
        public string $description = '',
        public bool $required = true,
        public string|int|float|bool|array|null $default = null,
    ) {}
}

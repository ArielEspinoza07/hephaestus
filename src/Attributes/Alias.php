<?php

declare(strict_types=1);

namespace Hephaestus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Alias
{
    public function __construct(
        public string $value,
    ) {}
}

<?php

declare(strict_types=1);

namespace Hephaestus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Signature
{
    public function __construct(
        public string $value,
    ) {}
}

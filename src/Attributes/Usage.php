<?php

declare(strict_types=1);

namespace Hephaestus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Usage
{
    /**
     * @param  list<string>  $value
     */
    public function __construct(
        public array $value,
    ) {}
}

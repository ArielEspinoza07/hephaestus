<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Support;

final readonly class CommandMetadata
{
    /**
     * @param list<string> $usages
     */
    public function __construct(
        public string $signature,
        public ?string $description = null,
        public ?string $help = null,
        public array $usages = [],
    ) {}
}

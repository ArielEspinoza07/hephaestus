<?php

declare(strict_types=1);

namespace Hephaestus\Metadata\Support;

final readonly class CommandMetadata
{
    /**
     * @param list<string> $usages
     * @param list<string> $aliases
     * @param list<InputMetadataContract> $parameters
     */
    public function __construct(
        public string $target,
        public string $signature,
        public ?string $description = null,
        public ?string $help = null,
        public bool $hasInput = false,
        public bool $hasOutput = false,
        public bool $hasStyleOutput = false,
        public array $usages = [],
        public array $aliases = [],
        public array $parameters = [],
    ) {}
}

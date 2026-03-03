<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Option;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:count')]
#[Description('Tests type casting')]
#[Output]
final readonly class CountCommand extends Command
{
    public function execute(
        #[Argument(description: 'An integer count')]
        int $count,
        #[Argument(description: 'A float ratio')]
        float $ratio,
        #[Option(description: 'Enable verbose mode')]
        bool $verbose,
    ): int {
        $this->output->writeln(sprintf('count=%d ratio=%.2f verbose=%s', $count, $ratio, $verbose ? 'true' : 'false'));

        return self::SUCCESS;
    }
}

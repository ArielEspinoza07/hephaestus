<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Help;
use Hephaestus\Attributes\Signature;
use Hephaestus\Attributes\Usage;
use Hephaestus\Console\Command;

#[Signature('app:test')]
#[Description('Test command')]
#[Help('This is a test command')]
#[Usage(['app:test'])]
final readonly class InvalidCommand extends Command
{
    public function execute(
        #[Argument]
        #[CompositeInput]
        string $name,
    ): int {
        return self::SUCCESS;
    }
}

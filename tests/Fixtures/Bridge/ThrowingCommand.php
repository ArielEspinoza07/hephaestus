<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\Bridge;

use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;
use RuntimeException;

#[Signature('bridge:throw')]
final readonly class ThrowingCommand extends Command
{
    public function execute(): int
    {
        throw new RuntimeException('deliberate failure');
    }
}

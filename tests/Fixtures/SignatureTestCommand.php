<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Input;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:test')]
#[Input]
#[Output]
final readonly class SignatureTestCommand extends Command
{
    public function execute(): int
    {
        return self::SUCCESS;
    }
}

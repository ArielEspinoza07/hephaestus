<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Help;
use Hephaestus\Attributes\Signature;
use Hephaestus\Attributes\Usage;

#[Signature('app:test')]
#[Description('Test command')]
#[Help('This is a test command')]
#[Usage(['app:test'])]
final readonly class NoExtendCommand
{
    public function __invoke(): int
    {
        return 0;
    }
}

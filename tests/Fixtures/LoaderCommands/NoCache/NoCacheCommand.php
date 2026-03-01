<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\LoaderCommands\NoCache;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:no-cache')]
#[Description('Command without cache')]
final readonly class NoCacheCommand extends Command
{
    public function __invoke(): int
    {
        return 0;
    }
}

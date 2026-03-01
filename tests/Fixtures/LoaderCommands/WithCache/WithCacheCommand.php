<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\LoaderCommands\WithCache;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:with-cache')]
#[Description('Command with cache enabled')]
final readonly class WithCacheCommand extends Command
{
    public function __invoke(): int
    {
        return 0;
    }
}

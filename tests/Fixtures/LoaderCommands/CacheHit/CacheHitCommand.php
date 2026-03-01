<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\LoaderCommands\CacheHit;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:cache-hit-real')]
#[Description('Real command (its metadata should be overridden by cache hit test)')]
final readonly class CacheHitCommand extends Command
{
    public function __invoke(): int
    {
        return 0;
    }
}

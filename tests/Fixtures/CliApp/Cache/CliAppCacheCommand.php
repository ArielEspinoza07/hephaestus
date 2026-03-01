<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\CliApp\Cache;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:cliapp-cache')]
#[Description('Command for CliApp cache tests')]
final readonly class CliAppCacheCommand extends Command
{
    public function execute(): int
    {
        return self::SUCCESS;
    }
}

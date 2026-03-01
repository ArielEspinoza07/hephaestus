<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\CliApp;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:cliapp-test')]
#[Description('Command for CliApp tests')]
final readonly class CliAppTestCommand extends Command
{
    public function __invoke(): int
    {
        return 0;
    }
}

<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\CliApp\Multi1;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:multi1')]
#[Description('Multi directory test command 1')]
final readonly class Multi1Command extends Command
{
    public function execute(): int
    {
        return self::SUCCESS;
    }
}

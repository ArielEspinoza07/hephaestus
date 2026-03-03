<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\CliApp\Multi2;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:multi2')]
#[Description('Multi directory test command 2')]
final readonly class Multi2Command extends Command
{
    public function execute(): int
    {
        return self::SUCCESS;
    }
}

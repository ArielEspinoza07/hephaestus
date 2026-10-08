<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\Testing;

use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;
use RuntimeException;

#[Signature('testing:fails-with-code')]
final readonly class FailsWithCodeCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): int
    {
        throw new RuntimeException('failed with a custom code', 3);
    }
}

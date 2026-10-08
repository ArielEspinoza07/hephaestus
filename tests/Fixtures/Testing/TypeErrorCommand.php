<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\Testing;

use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;
use TypeError;

#[Signature('testing:type-error')]
final readonly class TypeErrorCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): int
    {
        throw new TypeError('deliberate type error');
    }
}

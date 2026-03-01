<?php

declare(strict_types=1);

namespace Hephaestus\Console;

use Hephaestus\Console\Concerns\HasInput;
use Hephaestus\Console\Concerns\HasOutput;
use Hephaestus\Contracts\CommandInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @phpstan-consistent-constructor
 */
abstract readonly class Command implements CommandInterface
{
    use HasInput;
    use HasOutput;

    public function __construct(
        private ?InputInterface $input = null,
        private ?OutputInterface $output = null,
    ) {}
}

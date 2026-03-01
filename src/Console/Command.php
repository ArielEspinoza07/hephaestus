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

    protected const int SUCCESS = 0;
    protected const int FAILURE = 1;
    protected const int INVALID = 2;

    public function __construct(
        protected ?InputInterface $input = null,
        protected ?OutputInterface $output = null,
    ) {}
}

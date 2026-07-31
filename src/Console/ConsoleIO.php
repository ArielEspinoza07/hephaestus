<?php

declare(strict_types=1);

namespace Hephaestus\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleIO
{
    public function __construct(
        public ?InputInterface $input = null,
        public ?OutputInterface $output = null,
    ) {}
}

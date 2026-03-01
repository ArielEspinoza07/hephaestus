<?php

declare(strict_types=1);

namespace Hephaestus\Contracts;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandInterface
{
    /**
     * @return static
     */
    public function withInput(InputInterface $input): static;

    /**
     * @return static
     */
    public function withOutput(OutputInterface $output): static;
}

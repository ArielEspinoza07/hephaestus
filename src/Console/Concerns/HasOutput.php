<?php

declare(strict_types=1);

namespace Hephaestus\Console\Concerns;

use Symfony\Component\Console\Output\OutputInterface;

trait HasOutput
{
    public function withOutput(OutputInterface $output): static
    {
        $this->consoleIO->output = $output;

        return $this;
    }
}

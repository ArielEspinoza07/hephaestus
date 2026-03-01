<?php

declare(strict_types=1);

namespace Hephaestus\Console\Concerns;

use Symfony\Component\Console\Output\OutputInterface;

trait HasOutput
{
    /**
     * @return static
     */
    public function withOutput(OutputInterface $output): static
    {
        return new static(
            input: $this->input,
            output: $output,
        );
    }
}

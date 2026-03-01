<?php

declare(strict_types=1);

namespace Hephaestus\Console\Concerns;

use Symfony\Component\Console\Input\InputInterface;

trait HasInput
{
    /**
     * @return static
     */
    public function withInput(InputInterface $input): static
    {
        return new static(
            input: $input,
            output: $this->output,
        );
    }
}

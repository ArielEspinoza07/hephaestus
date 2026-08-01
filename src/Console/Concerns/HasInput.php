<?php

declare(strict_types=1);

namespace Hephaestus\Console\Concerns;

use Symfony\Component\Console\Input\InputInterface;

trait HasInput
{
    public function withInput(InputInterface $input): static
    {
        $this->consoleIO->input = $input;

        return $this;
    }
}

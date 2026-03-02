<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Option;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:greet')]
#[Description('Greets a user')]
#[Output]
final readonly class GreetCommand extends Command
{
    public function execute(
        #[Argument(description: 'The name of the user to greet')]
        string $name,
        #[Option(description: 'Whether to greet the user in a loud voice', shortcut: 'l')]
        bool $yell,
    ): int {
        $name = $yell ? mb_strtoupper($name) : $name;
        $this->output->writeln(sprintf('Hello, %s!', $name));

        return self::SUCCESS;
    }
}

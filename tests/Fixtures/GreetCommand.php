<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Option;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;

#[Signature('app:greet')]
#[Description('Greets a user')]
#[Output]
final class GreetCommand
{
    public function __invoke(
        #[Argument(description: 'The name of the user to greet')]
        string $name,
        #[Option(description: 'Whether to greet the user in a loud voice', shortcut: 'l')]
        bool $loud,
    ): int {
        return 0;
    }
}

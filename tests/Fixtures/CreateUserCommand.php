<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:create-user')]
#[Description('Creates a new user')]
#[Output]
final readonly class CreateUserCommand extends Command
{
    public function __invoke(
        #[CompositeInput]
        CreateUser $user
    ): int {
        return 0;
    }
}

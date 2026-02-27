<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;

#[Signature('app:create-user')]
#[Description('Creates a new user')]
#[Output]
final class CreateUserCommand
{
    public function __invoke(
        #[CompositeInput]
        CreateUser $user
    ): int {
        return 0;
    }
}

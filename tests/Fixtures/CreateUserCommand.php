<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Alias;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:create-user')]
#[Description('Creates a new user')]
#[Alias('app:create-user|app:add-user|app:new-user')]
#[Output]
final readonly class CreateUserCommand extends Command
{
    public function execute(
        #[CompositeInput]
        CreateUser $user
    ): int {
        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Option;

final readonly class CreateUser
{
    public function __construct(
        #[Argument(description: 'The name of the user to create')]
        public string $name,
        #[Argument(description: 'The email address of the user to create')]
        public string $email,
        #[Option(description: 'Whether the user should be an admin', shortcut: 'a')]
        public bool $isAdmin = false,
    ) {}
}

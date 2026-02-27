<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Argument;

final readonly class CreateUser
{
    public function __construct(
        #[Argument(description: 'The name of the user to create')]
        public string $name,
        #[Argument(description: 'The email address of the user to create')]
        public string $email,
    ) {}
}

<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;

#[Signature('app:send-email')]
#[Description('send an email')]
#[Output]
final class SendEmailCommand
{
    public function __invoke(
        #[CompositeInput]
        SendEmail $email,
    ): int {
        return 0;
    }
}

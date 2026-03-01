<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\CompositeInput;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('app:send-email')]
#[Description('send an email')]
#[Output]
final readonly class SendEmailCommand extends Command
{
    public function execute(
        #[CompositeInput]
        SendEmail $email,
    ): int {
        return self::SUCCESS;
    }
}

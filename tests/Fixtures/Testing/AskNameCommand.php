<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\Testing;

use Hephaestus\Attributes\Signature;
use Hephaestus\Attributes\Style;
use Hephaestus\Console\Command;

#[Signature('testing:ask-name')]
#[Style]
final readonly class AskNameCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): int
    {
        $name = $this->consoleIO->output->ask('What is your name?', 'stranger');
        $this->consoleIO->output->writeln(sprintf('Hello, %s!', $name));

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\Testing;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('testing:greet-with-dependency')]
#[Output]
final readonly class GreetWithDependencyCommand extends Command
{
    public function __construct(private Greeting $greeting)
    {
        parent::__construct();
    }

    public function execute(
        #[Argument(description: 'The name of the user to greet')]
        string $name,
    ): int {
        $this->consoleIO->output->writeln(sprintf('%s, %s!', $this->greeting->salutation, $name));

        return self::SUCCESS;
    }
}

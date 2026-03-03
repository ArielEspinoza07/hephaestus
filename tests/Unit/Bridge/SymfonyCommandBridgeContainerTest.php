<?php

declare(strict_types=1);

use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Tests\Fixtures\GreetCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

test('bridge uses container to resolve command when provided', function () {
    $greetCommand = new GreetCommand();

    $container = new class ($greetCommand) implements ContainerInterface {
        public function __construct(private readonly object $instance) {}

        public function get(string $id): object
        {
            return $this->instance;
        }

        public function has(string $id): bool
        {
            return true;
        }
    };

    $reader = new MetadataReader();
    $bridge = new SymfonyCommandBridge($container);

    $metadata = $reader->read(GreetCommand::class);
    $command = $bridge->convert($metadata);

    $input = new ArrayInput(['name' => 'World'], $command->getDefinition());
    $output = new BufferedOutput();

    $command->run($input, $output);

    expect($output->fetch())->toContain('Hello, World!');
});

test('bridge instantiates command directly when no container is provided', function () {
    $reader = new MetadataReader();
    $bridge = new SymfonyCommandBridge();

    $metadata = $reader->read(GreetCommand::class);
    $command = $bridge->convert($metadata);

    $input = new ArrayInput(['name' => 'World'], $command->getDefinition());
    $output = new BufferedOutput();

    $command->run($input, $output);

    expect($output->fetch())->toContain('Hello, World!');
});

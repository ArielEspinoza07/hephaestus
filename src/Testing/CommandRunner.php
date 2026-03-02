<?php

declare(strict_types=1);

namespace Hephaestus\Testing;

use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Console\Command;
use Hephaestus\Metadata\MetadataReader;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

final class CommandRunner
{
    /** @var array<string, mixed> */
    private array $args = [];

    /** @var array<string, mixed> */
    private array $options = [];

    /**
     * @param class-string<Command> $commandClass
     */
    private function __construct(private readonly string $commandClass) {}

    /**
     * @param class-string<Command> $commandClass
     */
    public static function for(string $commandClass): self
    {
        return new self($commandClass);
    }

    /**
     * @param array<string, mixed> $args
     */
    public function withArgs(array $args): self
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array<string, mixed> $options — without '--', auto-prefixed
     */
    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function run(): CommandResult
    {
        /** @var MetadataReader<object> $reader */
        $reader = new MetadataReader();
        $bridge = new SymfonyCommandBridge();

        $metadata = $reader->read($this->commandClass);
        $command = $bridge->convert($metadata);

        $input = array_merge($this->args, $this->prefixedOptions());

        $tester = new CommandTester($command);

        try {
            $tester->execute($input, ['decorated' => false, 'capture_stderr_separately' => true]);

            return new CommandResult(
                exitCode: $tester->getStatusCode(),
                output: $tester->getDisplay(true),
                errorOutput: $tester->getErrorOutput(true),
            );
        } catch (RuntimeException $e) {
            $message = $e->getMessage() . "\n";

            return new CommandResult(
                exitCode: 1,
                output: $message,
                errorOutput: $message,
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function prefixedOptions(): array
    {
        $prefixed = [];
        foreach ($this->options as $key => $value) {
            $normalizedKey = str_starts_with($key, '--') ? $key : '--' . $key;
            $prefixed[$normalizedKey] = $value;
        }

        return $prefixed;
    }
}

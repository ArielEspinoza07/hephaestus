<?php

declare(strict_types=1);

namespace Hephaestus\Testing;

use Hephaestus\CommandLoader;
use Hephaestus\Console\Command;
use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

final readonly class CommandRunner
{
    /**
     * @param class-string<Command> $commandClass
     * @param array<string, mixed> $args
     * @param array<string, mixed> $options
     * @param list<string> $inputs
     */
    private function __construct(
        private string $commandClass,
        private array $args = [],
        private array $options = [],
        private array $inputs = [],
        private ?ContainerInterface $container = null,
    ) {}

    /**
     * @param class-string<Command> $commandClass
     */
    public static function for(string $commandClass): self
    {
        return new self($commandClass);
    }

    /**
     * Merges the given arguments into the ones already set.
     *
     * @param array<string, mixed> $args
     */
    public function withArgs(array $args): self
    {
        return $this->copy(args: [...$this->args, ...$args]);
    }

    /**
     * Merges the given options into the ones already set. Keys without a leading dash are prefixed with '--'.
     *
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): self
    {
        return $this->copy(options: [...$this->options, ...$options]);
    }

    /**
     * Appends answers for interactive questions, consumed in order.
     *
     * @param list<string> $inputs
     */
    public function withInputs(array $inputs): self
    {
        return $this->copy(inputs: [...$this->inputs, ...$inputs]);
    }

    public function withContainer(ContainerInterface $container): self
    {
        return $this->copy(container: $container);
    }

    /**
     * @throws ReflectionException
     */
    public function run(): CommandResult
    {
        $loader = new CommandLoader(container: $this->container);
        $command = $loader->loadClasses([$this->commandClass])[0];

        $app = new Application();
        $app->setAutoExit(false);
        $app->addCommand($command);

        $tester = new ApplicationTester($app);
        $tester->setInputs($this->inputs);

        $tester->run(
            [
                'command' => $command->getName(),
                ...$this->args,
                ...$this->prefixedOptions(),
            ],
            [
                'decorated' => false,
                'capture_stderr_separately' => true,
                'interactive' => $this->inputs !== [],
            ],
        );

        return new CommandResult(
            exitCode: $tester->getStatusCode(),
            output: $tester->getDisplay(true),
            errorOutput: $tester->getErrorOutput(true),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function prefixedOptions(): array
    {
        $prefixed = [];
        foreach ($this->options as $key => $value) {
            $normalizedKey = str_starts_with($key, '-') ? $key : '--' . $key;
            $prefixed[$normalizedKey] = $value;
        }

        return $prefixed;
    }

    /**
     * @param array<string, mixed>|null $args
     * @param array<string, mixed>|null $options
     * @param list<string>|null $inputs
     */
    private function copy(
        ?array $args = null,
        ?array $options = null,
        ?array $inputs = null,
        ?ContainerInterface $container = null,
    ): self {
        return new self(
            commandClass: $this->commandClass,
            args: $args ?? $this->args,
            options: $options ?? $this->options,
            inputs: $inputs ?? $this->inputs,
            container: $container ?? $this->container,
        );
    }
}

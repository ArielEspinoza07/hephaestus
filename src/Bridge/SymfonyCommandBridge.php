<?php

declare(strict_types=1);

namespace Hephaestus\Bridge;

use Hephaestus\Metadata\Support\ArgumentMetadata;
use Hephaestus\Metadata\Support\CommandMetadata;
use Hephaestus\Metadata\Support\CompositeInputMetadata;
use Hephaestus\Metadata\Support\InputMetadataContract;
use Hephaestus\Metadata\Support\OptionMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final readonly class SymfonyCommandBridge
{
    public function __construct() {}

    public function convert(CommandMetadata $metadata): Command
    {
        $symfonyCommand = new Command($metadata->signature);
        if ($metadata->description) {
            $symfonyCommand->setDescription($metadata->description);
        }
        if ($metadata->help) {
            $symfonyCommand->setHelp($metadata->help);
        }
        if (count($metadata->usages)) {
            foreach ($metadata->usages as $usage) {
                $symfonyCommand->addUsage($usage);
            }
        }
        $symfonyCommand->setAliases($metadata->aliases);

        foreach ($metadata->parameters as $parameter) {
            if ($parameter instanceof ArgumentMetadata) {
                $this->addArgument($symfonyCommand, $parameter);
            }
            if ($parameter instanceof OptionMetadata) {
                $this->addOption($symfonyCommand, $parameter);
            }
            if ($parameter instanceof CompositeInputMetadata) {
                $this->addCompositeInput($symfonyCommand, $parameter);
            }
        }

        $symfonyCommand->setCode(function (InputInterface $input, OutputInterface $output) use ($metadata) {
            $class = new $metadata->target();
            if ($metadata->hasInput) {
                $class = $class->withInput($input);
            }
            if ($metadata->hasOutput) {
                $class = $class->withOutput($output);
            }
            if ($metadata->hasStyleOutput) {
                $class = $class->withOutput(new SymfonyStyle($input, $output));
            }

            $parameters = $this->convertSymfonyInputsToInternals($input, $metadata->parameters);

            return $class->execute(...$parameters);
        });

        return $symfonyCommand;
    }

    private function addArgument(Command $command, ArgumentMetadata $argument): void
    {
        $mode = $argument->isRequired ? InputArgument::REQUIRED : InputArgument::OPTIONAL;
        if ($argument->isArray()) {
            $mode = InputArgument::IS_ARRAY;
        }

        $command->addArgument(
            name: $argument->name,
            mode: $mode,
            description: $argument->description,
            default: $argument->default,
        );
    }

    private function addOption(Command $command, OptionMetadata $option): void
    {
        $mode = InputOption::VALUE_NONE;
        if ($option->acceptValue) {
            $mode = $option->hasDefault() ? InputOption::VALUE_OPTIONAL : InputOption::VALUE_REQUIRED;
        }
        if ($option->isArray()) {
            $mode = InputOption::VALUE_IS_ARRAY;
        }

        $command->addOption(
            name: $option->name,
            shortcut: $option->shortcut,
            mode: $mode,
            description: $option->description,
            default: $option->default,
        );
    }

    private function addCompositeInput(Command $command, CompositeInputMetadata $compositeInput): void
    {
        foreach ($compositeInput->properties as $property) {
            if ($property instanceof ArgumentMetadata) {
                $this->addArgument($command, $property);
            }
            if ($property instanceof OptionMetadata) {
                $this->addOption($command, $property);
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param list<InputMetadataContract> $parameters
     * @return array<int, mixed>
     */
    private function convertSymfonyInputsToInternals(InputInterface $input, array $parameters): array
    {
        $inputs = [];
        foreach ($parameters as $parameter) {
            if ($parameter instanceof ArgumentMetadata) {
                $inputs[] = $input->getArgument($parameter->name) ?? $parameter->default;
            }
            if ($parameter instanceof OptionMetadata) {
                $inputs[] = $input->getOption($parameter->name) ?? $parameter->default;
            }
            if ($parameter instanceof CompositeInputMetadata) {
                $compositeInputs = $this->convertSymfonyInputsToInternals($input, $parameter->properties);
                $inputs[] = new $parameter->target(...$compositeInputs);
            }
        }

        return $inputs;
    }
}

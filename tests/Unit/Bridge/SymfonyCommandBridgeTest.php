<?php

declare(strict_types=1);

use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Metadata\Support\ArgumentMetadata;
use Hephaestus\Metadata\Support\OptionMetadata;
use Hephaestus\Tests\Fixtures\GreetCommand;
use Symfony\Component\Console\Command\Command;

test('returns symfony command', function () {
    $reader = new MetadataReader();
    $bridge = new Hephaestus\Bridge\SymfonyCommandBridge();

    $metadata = $reader->read(GreetCommand::class);
    $command = $bridge->convert($metadata);

    expect($command)->toBeInstanceOf(Command::class)
        ->and($command->getName())->toBe($metadata->signature)
        ->and($command->getDescription())->toBe($metadata->description)
        ->and($command->getHelp())->toBeEmpty()
        ->and($command->getUsages())->toBe($metadata->usages)
        ->and($command->getAliases())->toBe($metadata->aliases);

    foreach ($metadata->parameters as $parameter) {
        if ($parameter instanceof ArgumentMetadata) {
            expect($command->getDefinition()->getArgument($parameter->name))->not()->toBeNull();
        }
        if ($parameter instanceof OptionMetadata) {
            expect($command->getDefinition()->getOption($parameter->name))->not()->toBeNull();
        }
    }
});

<?php

declare(strict_types=1);

use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Tests\Fixtures\Bridge\ThrowingCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

test('bridge propagates exceptions unmodified from execute()', function () {
    /** @var MetadataReader<object> $reader */
    $reader = new MetadataReader();
    $bridge = new SymfonyCommandBridge();

    $metadata = $reader->read(ThrowingCommand::class);
    $command = $bridge->convert($metadata);

    $input = new ArrayInput([]);
    $output = new NullOutput();

    expect(fn () => $command->run($input, $output))
        ->toThrow(RuntimeException::class, 'deliberate failure');
});

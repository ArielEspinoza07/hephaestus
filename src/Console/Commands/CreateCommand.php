<?php

declare(strict_types=1);

namespace Hephaestus\Console\Commands;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Option;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('make:command')]
#[Description('Create a new Hephaestus command')]
#[Output]
final readonly class CreateCommand extends Command
{
    public function execute(
        #[Argument(description: 'The name of the command class to create')]
        string $name,
        #[Option(description: 'Overwrite the file if it already exists', shortcut: 'f')]
        bool $force = false,
    ): int {
        if ($this->output === null) {
            return self::FAILURE;
        }

        $composerPath = getcwd() . '/composer.json';
        if (! file_exists($composerPath)) {
            $this->output->writeln('<error>composer.json not found in current directory.</error>');

            return self::FAILURE;
        }

        /** @var array{autoload?: array<string, mixed>} $composer */
        $composer = json_decode((string) file_get_contents($composerPath), true);

        if (empty($composer['autoload']['psr-4'])) {
            $this->output->writeln('<error>No autoload.psr-4 entry found in composer.json.</error>');

            return self::FAILURE;
        }

        /** @var array<string, string> $psr4 */
        $psr4 = $composer['autoload']['psr-4'];
        $rootNamespace = mb_rtrim((string) array_key_first($psr4), '\\');
        $rootDir = mb_rtrim((string) reset($psr4), '/');

        $className = str_ends_with($name, 'Command') ? $name : $name . 'Command';
        $namespace = $rootNamespace . '\\Commands';
        $directory = getcwd() . '/' . $rootDir . '/Commands';
        $filePath = $directory . '/' . $className . '.php';

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath) && ! $force) {
            $this->output->writeln('<comment>' . $filePath . ' already exists. Use --force to overwrite.</comment>');

            return self::FAILURE;
        }

        $stub = (string) file_get_contents(__DIR__ . '/stubs/command.stub');
        $content = str_replace(
            ['{{ namespace }}', '{{ className }}', '{{ signature }}'],
            [$namespace, $className, $this->toSignature($className)],
            $stub,
        );

        file_put_contents($filePath, $content);

        $this->output->writeln('<info>Command created: ' . $filePath . '</info>');

        return self::SUCCESS;
    }

    private function toSignature(string $className): string
    {
        $name = (string) preg_replace('/Command$/', '', $className);

        return mb_strtolower((string) preg_replace('/[A-Z]/', '-$0', lcfirst($name)));
    }
}

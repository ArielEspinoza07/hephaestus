<?php

declare(strict_types=1);

namespace Hephaestus\Console\Commands;

use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Option;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('make:entrypoint')]
#[Description('Create a CLI entrypoint script in bin/')]
#[Output]
final readonly class CreateEntrypointCommand extends Command
{
    public function execute(
        #[Argument(description: 'The name of the entrypoint file to create in bin/ (e.g. app)')]
        string $name,
        #[Option(description: 'The app name for CliApp::create()', acceptValue: true)]
        string $app = '',
        #[Option(description: 'The app version', acceptValue: true)]
        string $ver = '1.0.0',
        #[Option(description: 'Relative path to commands directory (e.g. src/Commands)', acceptValue: true)]
        string $dir = '',
        #[Option(description: 'Overwrite the file if it already exists', shortcut: 'f')]
        bool $force = false,
    ): int {
        if ($this->output === null) {
            return self::FAILURE;
        }

        $commandsDir = $this->resolveCommandsDir($dir);
        $appName = $app !== '' ? $app : ucfirst($name);
        $filePath = getcwd() . '/bin/' . $name;
        $binDir = getcwd() . '/bin';

        if (! is_dir($binDir)) {
            mkdir($binDir, 0755, true);
        }

        if (file_exists($filePath) && $force === false) {
            $this->output->writeln('<comment>' . $filePath . ' already exists. Use --force to overwrite.</comment>');

            return self::FAILURE;
        }

        $stub = (string) file_get_contents(__DIR__ . '/stubs/entrypoint.stub');
        $content = str_replace(
            ['{{ appName }}', '{{ version }}', '{{ commandsDir }}'],
            [$appName, $ver, $commandsDir],
            $stub,
        );

        file_put_contents($filePath, $content);
        @chmod($filePath, 0755);

        $this->output->writeln('<info>Entrypoint created: ' . $filePath . '</info>');
        $this->output->writeln('');
        $this->output->writeln('<comment>Next: add "bin": ["bin/' . $name . '"] to your composer.json, then run composer dump-autoload.</comment>');

        return self::SUCCESS;
    }

    private function resolveCommandsDir(string $dir): string
    {
        if ($dir !== '') {
            return $dir;
        }

        $composerPath = getcwd() . '/composer.json';
        if (file_exists($composerPath)) {
            /** @var array{autoload?: array<string, mixed>} $composer */
            $composer = json_decode((string) file_get_contents($composerPath), true);

            if (! empty($composer['autoload']['psr-4'])) {
                /** @var array<string, string> $psr4 */
                $psr4 = $composer['autoload']['psr-4'];
                $srcDir = mb_rtrim((string) reset($psr4), '/');

                return $srcDir . '/Commands';
            }
        }

        return 'src/Commands';
    }
}

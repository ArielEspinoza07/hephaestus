<?php

declare(strict_types=1);

namespace Hephaestus;

use Exception;
use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Metadata\MetadataReader;
use ReflectionException;
use Symfony\Component\Console\Application;

final readonly class CliApp
{
    private Application $app;

    private function __construct(private string $name, private string $version)
    {
        $this->app = new Application($this->name, $this->version);
    }

    public static function create(string $name, string $version = '1.0.0'): self
    {
        return new self(
            name: $name,
            version: $version,
        );
    }

    /**
     * @throws ReflectionException
     */
    public function registerCommands(string $directory): self
    {
        $reader = new MetadataReader();
        $bridge = new SymfonyCommandBridge();

        $files = glob(mb_rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') ?: [];

        $commands = [];

        foreach ($files as $file) {
            $className = $this->resolveClassName($file);
            if ($className !== null) {
                $commands[] = $reader->read($className);
            }
        }

        $symfonyCommands = [];

        foreach ($commands as $metadata) {
            $symfonyCommands[] = $bridge->convert($metadata);
        }

        $this->app->addCommands($symfonyCommands);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function run(): int
    {
        return $this->app->run();
    }

    private function resolveClassName(string $file): ?string
    {
        $before = get_declared_classes();
        require_once $file;
        $after = get_declared_classes();

        $new = array_values(array_diff($after, $before));

        return count($new) === 1 ? $new[0] : null;
    }
}

<?php

declare(strict_types=1);

namespace Hephaestus;

use Exception;
use Hephaestus\Cache\CommandCache;
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
     * @param string|list<string> $directories
     * @throws ReflectionException
     */
    public function registerCommands(string|array $directories, ?string $cachePath = null): self
    {
        $loader = new CommandLoader(
            cache: $cachePath !== null ? new CommandCache($cachePath) : null,
        );

        foreach ((array) $directories as $directory) {
            $this->app->addCommands($loader->load($directory));
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function run(): int
    {
        return $this->app->run();
    }


}

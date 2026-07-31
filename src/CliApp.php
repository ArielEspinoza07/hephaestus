<?php

declare(strict_types=1);

namespace Hephaestus;

use Exception;
use Hephaestus\Cache\CommandCache;
use Hephaestus\Console\Command;
use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\Console\Application;

final class CliApp
{
    private Application $app;

    private ?ContainerInterface $container = null;

    private ?string $cachePath = null;

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

    public function withContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function withCache(string $cachePath): self
    {
        $this->cachePath = $cachePath;

        return $this;
    }

    /**
     * @param string|list<string> $directories
     * @throws ReflectionException
     */
    public function registerCommandsDirectory(string|array $directories): self
    {
        $loader = $this->loader();

        foreach ((array) $directories as $directory) {
            $this->app->addCommands($loader->loadDirectory($directory));
        }

        return $this;
    }

    /**
     * @param class-string<Command> $command
     * @throws ReflectionException
     */
    public function registerCommand(string $command): self
    {
        return $this->registerCommands([$command]);
    }

    /**
     * @param list<class-string<Command>> $commands
     * @throws ReflectionException
     */
    public function registerCommands(array $commands): self
    {
        $this->app->addCommands($this->loader()->loadClasses($commands));

        return $this;
    }

    /**
     * @throws Exception
     */
    public function run(): int
    {
        return $this->app->run();
    }

    private function loader(): CommandLoader
    {
        return new CommandLoader(
            cache: $this->cachePath !== null ? new CommandCache($this->cachePath) : null,
            container: $this->container,
        );
    }
}

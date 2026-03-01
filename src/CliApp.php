<?php

declare(strict_types=1);

namespace Hephaestus;

use Exception;
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
        $this->app->addCommands(new CommandLoader()->load($directory));

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

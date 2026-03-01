<?php

declare(strict_types=1);

namespace Hephaestus;

use Exception;
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

    public function registerCommands(string $directory): self
    {
        /** TODO:
         * 1. Read all the file names in the given directory
         * 2. Create variable to store CommandMetadata: $commands
         * 3. Iterate through the file names
         * 4. Use MetadataReader to obtain CommandMetadata and store it in $commands.
         * 5. Use SymfonyCommandBridge to convert CommandMetadata to Symfony Command
         * 6. Register the Symfony Command with the Application
         */

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

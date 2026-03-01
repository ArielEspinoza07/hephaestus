<?php

declare(strict_types=1);

namespace Hephaestus;

use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Metadata\MetadataReader;
use ReflectionException;
use Symfony\Component\Console\Command\Command;

final readonly class CommandLoader
{
    /**
     * @return list<Command>
     * @throws ReflectionException
     */
    public function load(string $directory): array
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

        return $symfonyCommands;
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

<?php

declare(strict_types=1);

namespace Hephaestus;

use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Cache\CommandCache;
use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Metadata\Support\CommandMetadata;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

final readonly class CommandLoader
{
    public function __construct(
        private ?CommandCache $cache = null,
    ) {}

    /**
     * @return list<Command>
     *
     * @throws ReflectionException
     */
    public function load(string $directory): array
    {
        $reader = new MetadataReader();
        $bridge = new SymfonyCommandBridge();
        $finder = new Finder();

        $files = $finder
            ->files()
            ->in(mb_rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)
            ->name('*Command.php');

        $commands = [];

        foreach ($files as $file) {
            $className = $this->resolveClassName($file->getRealPath());
            if ($className !== null) {
                $commands[] = $this->readMetadata($reader, $file->getRealPath(), $className);
            }
        }

        $this->cache?->flush();

        $symfonyCommands = [];

        foreach ($commands as $metadata) {
            $symfonyCommands[] = $bridge->convert($metadata);
        }

        return $symfonyCommands;
    }

    /**
     * @param MetadataReader<object> $reader
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    private function readMetadata(MetadataReader $reader, string $file, string $className): CommandMetadata
    {
        if ($this->cache !== null) {
            $cached = $this->cache->get($file);
            if ($cached !== null) {
                return $cached;
            }
        }

        $metadata = $reader->read($className);
        $this->cache?->set($file, $metadata);

        return $metadata;
    }

    private function resolveClassName(string $file): ?string
    {
        $before = get_declared_classes();
        require_once $file;
        $after = get_declared_classes();

        $realFile = realpath($file);

        foreach (array_diff($after, $before) as $class) {
            try {
                if (realpath(new ReflectionClass($class)->getFileName()) === $realFile) {
                    return $class;
                }
            } catch (ReflectionException) {
                // skip internal or anonymous classes without a file
            }
        }

        return null;
    }
}

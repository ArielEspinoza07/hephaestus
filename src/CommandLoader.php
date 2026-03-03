<?php

declare(strict_types=1);

namespace Hephaestus;

use Hephaestus\Bridge\SymfonyCommandBridge;
use Hephaestus\Cache\CommandCache;
use Hephaestus\Metadata\MetadataReader;
use Hephaestus\Metadata\Support\CommandMetadata;
use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

final readonly class CommandLoader
{
    public function __construct(
        private ?CommandCache $cache = null,
        private ?ContainerInterface $container = null,
    ) {}

    /**
     * @return list<Command>
     * @throws ReflectionException
     */
    public function load(string $directory): array
    {
        $reader = new MetadataReader();
        $bridge = new SymfonyCommandBridge($this->container);
        $finder = new Finder();

        $files = $finder
            ->files()
            ->in(mb_rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)
            ->name('*Command.php');

        $commands = [];

        foreach ($files as $file) {
            $className = $this->resolveClassName($file->getRealPath());
            if ($className !== null) {
                require_once $file->getRealPath();
                /** @var class-string $className */
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
        $tokens = token_get_all((string) file_get_contents($file), TOKEN_PARSE);

        $namespace = '';
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                $i += 2; // skip T_NAMESPACE + whitespace
                $namespace = '';
                while ($i < $count && $tokens[$i] !== ';' && $tokens[$i] !== '{') {
                    if (is_array($tokens[$i])) {
                        $namespace .= $tokens[$i][1];
                    }
                    $i++;
                }
                continue;
            }

            if ($token[0] === T_CLASS) {
                $j = $i + 1;
                while ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                    $j++;
                }
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $class = $tokens[$j][1];
                    return $namespace !== '' ? $namespace . '\\' . $class : $class;
                }
            }
        }

        return null;
    }
}

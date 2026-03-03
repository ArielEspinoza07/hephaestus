<?php

declare(strict_types=1);

namespace Hephaestus\Cache;

use Hephaestus\Metadata\Support\ArgumentMetadata;
use Hephaestus\Metadata\Support\CommandMetadata;
use Hephaestus\Metadata\Support\CompositeInputMetadata;
use Hephaestus\Metadata\Support\OptionMetadata;
use RuntimeException;

final class CommandCache
{
    /** @var array<string, array{hash: string, metadata: CommandMetadata}> */
    private array $data = [];

    private bool $loaded = false;

    private bool $dirty = false;

    public function __construct(private readonly string $path) {}

    public function get(string $file): ?CommandMetadata
    {
        $this->ensureLoaded();

        $entry = $this->data[$file] ?? null;
        if ($entry === null) {
            return null;
        }

        if ($this->isStale($file, $entry['hash'])) {
            unset($this->data[$file]);
            $this->dirty = true;

            return null;
        }

        return $entry['metadata'];
    }

    public function set(string $file, CommandMetadata $metadata): void
    {
        $hash = hash_file('xxh3', $file);
        if ($hash === false) {
            throw new RuntimeException(sprintf('Failed to hash file: %s', $file));
        }

        $this->data[$file] = [
            'hash'     => $hash,
            'metadata' => $metadata,
        ];
        $this->dirty = true;
    }

    public function flush(): void
    {
        if (!$this->dirty) {
            return;
        }
        file_put_contents($this->path, serialize($this->data));
        $this->dirty = false;
    }

    private function ensureLoaded(): void
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;

        if (!file_exists($this->path)) {
            return;
        }

        $raw = file_get_contents($this->path);
        if ($raw === false) {
            return;
        }

        $decoded = unserialize($raw, [
            'allowed_classes' => [
                CommandMetadata::class,
                ArgumentMetadata::class,
                OptionMetadata::class,
                CompositeInputMetadata::class,
            ],
        ]);

        if (is_array($decoded)) {
            $this->data = $decoded;
        }
    }

    private function isStale(string $file, string $cachedHash): bool
    {
        return hash_file('xxh3', $file) !== $cachedHash;
    }
}

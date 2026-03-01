<?php

declare(strict_types=1);

namespace Hephaestus\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Option
{
    /**
     * @param string|int|float|bool|array<int|string, mixed>|null $default
     * @param string|list<string>|null $shortcut
     */
    public function __construct(
        public string $description = '',
        public bool $acceptValue = false,
        public string|int|float|bool|array|null $default = null,
        public array|string|null $shortcut = null,
    ) {
        if (is_string($this->shortcut) && mb_strlen($this->shortcut) !== 1) {
            throw new InvalidArgumentException('Shortcut must be exactly 1 character');
        }
        if (is_array($this->shortcut) && empty($this->shortcut)) {
            throw new InvalidArgumentException('Shortcut cannot be empty.');
        }
        if (is_array($this->shortcut)) {
            foreach ($this->shortcut as $item) {
                /** @phpstan-ignore-next-line */
                if (!is_string($item)) {
                    throw new InvalidArgumentException('Shortcut must be a string');
                }
                if (mb_strlen($item) !== 1) {
                    throw new InvalidArgumentException('Shortcut must be exactly 1 character');
                }
            }
        }
    }
}

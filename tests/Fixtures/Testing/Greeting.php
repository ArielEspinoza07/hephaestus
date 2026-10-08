<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures\Testing;

final readonly class Greeting
{
    public function __construct(public string $salutation) {}
}

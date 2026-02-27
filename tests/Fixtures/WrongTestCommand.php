<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Help;
use Hephaestus\Attributes\Usage;

#[Description('Test command')]
#[Help('This is a test command')]
#[Usage(['app:test'])]
final class WrongTestCommand {}

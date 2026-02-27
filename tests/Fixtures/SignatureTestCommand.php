<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Input;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;

#[Signature('app:test')]
#[Input]
#[Output]
final class SignatureTestCommand {}

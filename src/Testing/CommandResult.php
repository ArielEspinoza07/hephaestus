<?php

declare(strict_types=1);

namespace Hephaestus\Testing;

use PHPUnit\Framework\Assert;

final readonly class CommandResult
{
    public function __construct(
        private int $exitCode,
        private string $output,
        private string $errorOutput,
    ) {}

    public function exitCode(): int
    {
        return $this->exitCode;
    }

    public function output(): string
    {
        return str_replace("\r\n", "\n", $this->output);
    }

    public function errorOutput(): string
    {
        return str_replace("\r\n", "\n", $this->errorOutput);
    }

    public function isSuccessful(): bool
    {
        return $this->exitCode === 0;
    }

    public function assertSuccessful(): static
    {
        Assert::assertSame(0, $this->exitCode, sprintf(
            'Expected exit code 0, got %d.',
            $this->exitCode,
        ));

        return $this;
    }

    public function assertFailed(): static
    {
        Assert::assertNotSame(0, $this->exitCode, 'Expected command to fail, but it exited with code 0.');

        return $this;
    }

    public function assertExitCode(int $expected): static
    {
        Assert::assertSame($expected, $this->exitCode, sprintf(
            'Expected exit code %d, got %d.',
            $expected,
            $this->exitCode,
        ));

        return $this;
    }

    public function assertOutputContains(string $needle): static
    {
        Assert::assertStringContainsString($needle, $this->output(), sprintf(
            'Expected output to contain "%s".',
            $needle,
        ));

        return $this;
    }

    public function assertOutputEquals(string $expected): static
    {
        Assert::assertSame($expected, $this->output(), 'Output does not match expected value.');

        return $this;
    }

    public function assertErrorOutputContains(string $needle): static
    {
        Assert::assertStringContainsString($needle, $this->errorOutput(), sprintf(
            'Expected error output to contain "%s".',
            $needle,
        ));

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Hephaestus\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base class for integration tests.
 *
 * Provides helper methods for executing CLI commands
 * and verifying their output.
 */
abstract class IntegrationTestCase extends TestCase
{
    protected string $phpBinary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phpBinary = PHP_BINARY;
    }

    /**
     * Execute a CLI command and return output, exit code, and errors.
     *
     * @return array{stdout: string, stderr: string, exitCode: int}
     */
    protected function executeCommand(string $command): array
    {
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException("Failed to execute command: {$command}");
        }

        fclose($pipes[0]); // Close stdin

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'exitCode' => $exitCode,
        ];
    }
}

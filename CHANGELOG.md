# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2026-03-16

### Fixed

- `CommandRunner` now catches any `\Throwable` (not only `RuntimeException`) and returns a `CommandResult` with `exitCode 1`, preventing uncaught errors from leaking in tests

### Added

- `SymfonyCommandBridgeErrorTest` — regression test locking that the bridge propagates exceptions unmodified from `execute()`
- `CommandRunnerTest` — test verifying `\Throwable` is converted to `CommandResult`
- `ThrowingCommand` fixture for bridge/runner error tests

## [1.1.0] - 2026-03-03

### Added

- `#[Style]` attribute — declares SymfonyStyle output helpers on a command class
- `CliApp::withContainer(ContainerInterface $container)` — PSR-11 container support for command instantiation
- `registerCommands()` now accepts `string|array` — register commands from multiple directories in a single call
- Automatic type casting for CLI arguments and options (`int`, `float`, `bool`)

### Changed

- PHPStan raised from level 6 to level 8

## [1.0.0] - 2026-03-01

### Added

- `CliApp` — fluent API for creating and running Symfony CLI applications with optional file-based command caching
- `CommandLoader` — discovers and loads command classes from a directory
- `CommandCache` — file-based serialize/unserialize cache keyed by absolute file path
- `SymfonyCommandBridge` — converts Hephaestus `Command` instances into Symfony `Command` objects
- `Command` base class with `HasInput` and `HasOutput` traits for type-safe I/O helpers (`info`, `error`, `line`, `ask`, `confirm`, …)
- `#[Signature]` attribute — declares the command name/signature
- `#[Description]` attribute — sets the command description
- `#[Help]` attribute — sets the command help text
- `#[Usage]` attribute — adds command usage examples
- `#[Alias]` attribute — declares command aliases
- `#[Input]` and `#[Output]` attributes — configure I/O behaviour
- `#[Argument]` attribute — declarative argument definitions
- `#[Option]` attribute — declarative option definitions
- `#[CompositeInput]` attribute — groups multiple inputs as a composite
- `MetadataReader` — reads and resolves attribute-based metadata from command classes
- `CommandMetadata` DTO — immutable value object holding resolved name, description, arguments, options, and aliases
- `ArgumentMetadata` / `OptionMetadata` — immutable DTOs for individual argument and option metadata
- `CommandRunner` / `CommandResult` — test helpers for running commands in isolation
- Comprehensive Pest test suite
- PHPStan level 6 static analysis
- Laravel Pint PSR-12 code style enforcement
- MIT license

[Unreleased]: https://github.com/arielespinoza07/hephaestus/compare/v1.1.1...HEAD
[1.1.1]: https://github.com/arielespinoza07/hephaestus/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/arielespinoza07/hephaestus/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/arielespinoza07/hephaestus/releases/tag/1.0.0

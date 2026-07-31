# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.0] - 2026-07-31

### Added

- `ConsoleIO` — mutable holder for `InputInterface`/`OutputInterface`, referenced by `Command` via a new `protected ConsoleIO $consoleIO` property

### Changed

- **Breaking:** `Command::__construct()` now accepts `?ConsoleIO $consoleIO = null` instead of `?InputInterface $input = null, ?OutputInterface $output = null`; code that manually instantiates a `Command` subclass with positional or named `input`/`output` arguments must pass a `ConsoleIO` instance instead
- `#[Input]`/`#[Output]`/`#[Style]` now inject into `$this->consoleIO->input` / `$this->consoleIO->output` instead of `$this->input` / `$this->output`

### Fixed

- `HasInput::withInput()` and `HasOutput::withOutput()` no longer reconstruct the command via `new static(input: ..., output: ...)`, which assumed every command constructor matched the base `Command` signature exactly and broke (or silently discarded resolved dependencies) for commands with their own constructor-injected dependencies; they now mutate `$consoleIO` in place and return the same instance

## [2.0.1] - 2026-07-30

### Changed

- CI: added a `lowest-dependencies` job that runs `composer update --prefer-lowest` on PHP 8.3 to verify the package actually works at the floor of the `symfony/console`/`symfony/finder` constraints introduced in 2.0.0

### Fixed

- `CommandLoader::loadDirectory()` now trims both `/` and `\` from the input directory path instead of only the separator native to the host OS, so a path built with the "wrong" separator for the current platform is still normalized correctly
- Stale `PHP 8.5+` / `Symfony Console 8.0+` references in the README, left over from the 2.0.0 version-floor change

## [2.0.0] - 2026-07-30

### Added

- `CliApp::registerCommand(string $command)` — register a single command by class name, without scanning a directory
- `CliApp::registerCommands(array $commands)` — register multiple commands by class name
- `CliApp::withCache(string $cachePath)` — fluent cache configuration, applied to `registerCommandsDirectory()`, `registerCommand()`, and `registerCommands()` alike
- `symfony/finder` as an explicit dependency (previously pulled in transitively via `symfony/console`)

### Changed

- **Breaking:** `CliApp::registerCommands(string|array $directories, ?string $cachePath = null)` renamed to `registerCommandsDirectory()`; the `$cachePath` parameter was removed in favor of `withCache()`
- **Breaking:** `CommandLoader::load()` renamed to `loadDirectory()`
- Lowered minimum PHP version from `^8.5` to `^8.3`; widened `symfony/console` from `^8.0.6` to `^7.4|^8.0`
- CI matrix now tests PHP 8.3, 8.4, and 8.5 (previously only 8.5)
- `CommandLoader::loadClasses()` now shares the same file-content-hash cache as `loadDirectory()` (resolved via `ReflectionClass::getFileName()`) instead of always re-reflecting
- `MetadataReader::checkParentClass()` now uses `isSubclassOf()` instead of a direct-parent-only check, correctly supporting multi-level command class inheritance
- Replaced `mb_*` string functions (`mb_strlen`, `mb_rtrim`, `mb_strtolower`, `mb_trim`) with their ASCII equivalents, consistent with the lowered PHP floor
- `pint.json`: removed the `mb_str_functions` rule

### Fixed

- `MetadataReader::checkExecuteMethod()` now also verifies the `execute` method is `public`, throwing `RuntimeException` otherwise (previously only checked that it existed)
- Replaced PHP 8.5-only syntax (`array_first()`, `array_last()`, the `|>` pipe operator) with PHP 8.3-compatible equivalents — these were breaking the CI matrix on PHP 8.3/8.4 despite the lowered version floor

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

[Unreleased]: https://github.com/arielespinoza07/hephaestus/compare/v3.0.0...HEAD
[3.0.0]: https://github.com/arielespinoza07/hephaestus/compare/v2.0.1...v3.0.0
[2.0.1]: https://github.com/arielespinoza07/hephaestus/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/arielespinoza07/hephaestus/compare/v1.1.1...v2.0.0
[1.1.1]: https://github.com/arielespinoza07/hephaestus/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/arielespinoza07/hephaestus/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/arielespinoza07/hephaestus/releases/tag/1.0.0

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-03-01

### Added

- `CliApp` — fluent API for creating and running Symfony CLI applications with optional file-based command caching
- `CommandLoader` — discovers and loads command classes from a directory, with `resolveClassName` powered by `ReflectionClass`
- `CommandCache` — file-based serialize/unserialize cache keyed by absolute file path
- `CommandBridge` — converts Hephaestus `Command` instances into Symfony `Command` objects
- `Command` base class with `InputTrait` and `OutputTrait` for type-safe I/O helpers (`info`, `error`, `line`, `ask`, `confirm`, …)
- `#[ConsoleCommand]` attribute — declares name, description, and aliases on a command class
- `#[Argument]` and `#[CompositeInput]` attributes — declarative argument definitions
- `#[Option]` attribute — declarative option definitions
- `MetadataReader` — reads and resolves attribute-based metadata from command classes
- `CommandMetadata` DTO — immutable value object holding resolved name, description, arguments, options, and aliases
- `ArgumentMetadata` / `OptionMetadata` — immutable DTOs for individual argument and option metadata
- `createEntrypointCommand` — factory helper for bootstrapping a Symfony Console entry-point
- Comprehensive Pest test suite: 52 tests, 162 assertions
- PHPStan level 6 static analysis
- Laravel Pint PSR-12 code style enforcement
- MIT license

[1.0.0]: https://github.com/arielespinoza07/hephaestus/releases/tag/v1.0.0

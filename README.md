# Hephaestus

[![CI](https://github.com/arielespinoza07/hephaestus/actions/workflows/ci.yml/badge.svg)](https://github.com/arielespinoza07/hephaestus/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/arielespinoza07/hephaestus.svg)](https://packagist.org/packages/arielespinoza07/hephaestus)
[![Total Downloads](https://img.shields.io/packagist/dt/arielespinoza07/hephaestus.svg)](https://packagist.org/packages/arielespinoza07/hephaestus)
[![PHP Version](https://img.shields.io/packagist/php-v/arielespinoza07/hephaestus.svg)](https://packagist.org/packages/arielespinoza07/hephaestus)
[![License](https://img.shields.io/packagist/l/arielespinoza07/hephaestus.svg)](LICENSE)

> Build powerful CLI tools with ease

An attribute-driven CLI framework built on Symfony Console.
Define commands with PHP attributes — Hephaestus handles the rest.

---

## Philosophy

**Symfony Console is powerful but verbose.**

Hephaestus gives you:
- 🎯 Simple API for simple tasks
- 🔒 Type-safe everywhere (PHP 8.5+)
- ⚡ Fast to write, easy to test
- 🔧 Full Symfony power when needed
- 🏗️ Clean architecture — SOLID, DRY, YAGNI, KISS
- 🧪 Test-friendly design
- 🚀 Built-in scaffolding — generate commands and entrypoints instantly

---

## Requirements

- PHP 8.5+
- Symfony Console 8.0+
- psr/container 2.0+  (pulled in automatically via Composer)

---

## Installation

```bash
composer require arielespinoza07/hephaestus
```

---

## Quick Start

### 1. Create a command

```php
<?php

declare(strict_types=1);

use Hephaestus\Console\Command;
use Hephaestus\Attributes\Signature;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Option;

#[Signature('app:greet')]
#[Description('Greets a user by name')]
#[Output]
final readonly class GreetCommand extends Command
{
    public function execute(
        #[Argument(description: 'The name of the user to greet')]
        string $name,
        #[Option(description: 'Shout the greeting', shortcut: 'l')]
        bool $yell = false,
    ): int {
        $greeting = sprintf('Hello, %s!', $name);
        $this->output->writeln($yell ? mb_strtoupper($greeting) : $greeting);

        return self::SUCCESS;
    }
}
```

### 2. Bootstrap your CLI app

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Hephaestus\CliApp;

exit(
    CliApp::create('MyApp', '1.0.0')
        ->registerCommands(__DIR__ . '/src/Commands')
        ->run()
);
```

### 3. Run it

```bash
php bin/console app:greet John
# Hello, John!

php bin/console app:greet John --yell
# HELLO, JOHN!

php bin/console app:greet John -l
# HELLO, JOHN!
```

---

## Scaffolding

Hephaestus ships a `bin/hephaestus` binary with two generators to get you started without writing boilerplate.

### `make:command`

Generate a command class skeleton inside your project's `src/Commands/` directory (auto-detected from `composer.json`).

```bash
./vendor/bin/hephaestus make:command Greet
# → src/Commands/GreetCommand.php

./vendor/bin/hephaestus make:command Greet --force   # overwrite existing
./vendor/bin/hephaestus make:command Greet -f        # shortcut
```

The generated file:

```php
<?php

declare(strict_types=1);

namespace App\Commands;

use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Output;
use Hephaestus\Attributes\Signature;
use Hephaestus\Console\Command;

#[Signature('greet')]
#[Description('')]
#[Output]
final readonly class GreetCommand extends Command
{
    public function execute(): int
    {
        return self::SUCCESS;
    }
}
```

The signature is auto-derived from the class name (`CreateUserCommand` → `create-user`) feel free to change it.

---

### `make:entrypoint`

Generate an executable CLI entrypoint in `bin/`.

```bash
./vendor/bin/hephaestus make:entrypoint app
# → bin/app  (executable, chmod 0755)

# Full options
./vendor/bin/hephaestus make:entrypoint app \
    --app="My App" \
    --ver=2.0.0 \
    --dir=src/Commands \
    --force
```

| Option | Default | Description |
|--------|---------|-------------|
| `--app` | `ucfirst($name)` | App name passed to `CliApp::create()` |
| `--ver` | `1.0.0` | App version |
| `--dir` | auto-detected from `composer.json` | Relative path to commands directory |
| `--force` / `-f` | `false` | Overwrite if file already exists |

The generated file:

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Hephaestus\CliApp;

exit(
    CliApp::create('App', '1.0.0')
        ->registerCommands(__DIR__ . '/../src/Commands')
        ->run()
);
```

---

## How It Works

Hephaestus reads PHP attributes on your command class and its `execute()` method parameters to automatically wire up the Symfony Console command — no `configure()`, no `$input->getArgument()`, no `$input->getOption()`.

```
PHP Attributes
     │
     ▼
MetadataReader  ──►  CommandMetadata
                           │
                           ▼
                  SymfonyCommandBridge  ──►  Symfony Command
                                                   │
                                                   ▼
                                             CLI / CommandTester
```

---

## Attributes Reference

### Class-level attributes

| Attribute                            | Required | Description |
|--------------------------------------|----------|-------------|
| `#[Signature('app:name')]`           | ✅ | CLI command name |
| `#[Description('...')]`              | | Short description shown in the command list |
| `#[Help('...')]`                     | | Extended help shown by `help <command>` |
| `#[Usage(['app:name arg1', '...'])]` | | Example usages shown in help output |
| `#[Alias('alias1\|alias2')]`         | | Pipe-separated command aliases |
| `#[Input]`                           | | Inject `InputInterface` into `$this->input` |
| `#[Output]`                          | | Inject `OutputInterface` into `$this->output` |
| `#[Style]`                           | | Inject `SymfonyStyle ` into `$this->output` |

### Parameter-level attributes

#### `#[Argument]`

Maps a positional CLI argument (`command arg`).

```php
#[Argument(
    description: 'The user name',   // shown in help
    required: true,                  // default: true
    default: null,                   // value when optional and not provided
)]
string $name
```

> **Type casting**: When the parameter type is `int`, `float`, or `bool`, the raw CLI string is automatically cast before being passed to `execute()`.

#### `#[Option]`

Maps a named CLI option (`command --option` or `command --option=value`).

```php
#[Option(
    description: 'Shout the greeting',  // shown in help
    acceptValue: false,                  // true: --format=json, false: --verbose flag
    default: null,                       // default value when not provided
    shortcut: 'l',                       // single char or array of chars
)]
bool $yell
```

> **Type casting**: When the parameter type is `int`, `float`, or `bool`, the raw CLI string is automatically cast before being passed to `execute()`.

#### `#[CompositeInput]`

Groups multiple arguments/options into a single DTO, keeping `execute()` clean.

```php
// DTO
final readonly class CreateUserInput
{
    public function __construct(
        #[Argument(description: 'User name')]
        public string $name,
        #[Argument(description: 'User email')]
        public string $email,
        #[Option(description: 'Grant admin role', shortcut: 'a')]
        public bool $admin = false,
    ) {}
}

// Command
public function execute(
    #[CompositeInput]
    CreateUserInput $input
): int {
    // ...
}
```

---

## Command Class

All commands extend `Hephaestus\Console\Command` and must be `final readonly`.

```php
abstract readonly class Command
{
    // Available when #[Input] is declared on the class
    protected ?InputInterface $input;

    // Available when #[Output] is declared on the class
    protected ?OutputInterface $output;

    // Exit code constants
    protected const int SUCCESS = 0;
    protected const int FAILURE = 1;
    protected const int INVALID = 2;
}
```

---

## CliApp

Bootstrap a complete CLI application with auto-discovery of commands.

```php
CliApp::create(string $name, string $version = '1.0.0'): self
```

```php
// Scan one or multiple directories and register all commands
->registerCommands(string|array $directories, ?string $cachePath = null): self

// Examples
->registerCommands(__DIR__ . '/src/Commands')
->registerCommands([__DIR__ . '/src/Commands', __DIR__ . '/src/Plugins'])

// With metadata cache for faster startup
->registerCommands(__DIR__ . '/src/Commands', __DIR__ . '/var/cache/commands.cache')

// Resolve commands through a PSR-11 container (optional)
->withContainer(ContainerInterface $container): self

// Run the CLI application
->run(): int
```

`registerCommands()` returns `$this` for fluent chaining and can be called multiple times for multiple directories.

`withContainer()` enables dependency injection — commands are resolved via `$container->get()` instead of `new ClassName()`.

---

## Testing

Hephaestus ships a first-class testing API built on top of Symfony's `CommandTester`.

### CommandRunner

```php
use Hephaestus\Testing\CommandRunner;

$result = CommandRunner::for(GreetCommand::class)
    ->withArgs(['name' => 'John'])         // positional arguments
    ->withOptions(['yell' => true])        // options (-- prefix added automatically)
    ->run();
```

### CommandResult assertions

```php
$result->assertSuccessful();                        // exit code === 0
$result->assertFailed();                            // exit code !== 0
$result->assertExitCode(int $expected);             // exact exit code
$result->assertOutputContains(string $needle);      // stdout contains substring
$result->assertOutputEquals(string $expected);      // stdout exact match
$result->assertErrorOutputContains(string $needle); // stderr contains substring
```

All assertion methods return `$this` for fluent chaining:

```php
$result
    ->assertSuccessful()
    ->assertExitCode(0)
    ->assertOutputEquals('Hello, John!');
```

### Inspecting output directly

```php
$result->exitCode();      // int
$result->output();        // string (stdout, trimmed)
$result->errorOutput();   // string (stderr, trimmed)
$result->isSuccessful();  // bool
```

### Full test example (PestPHP)

```php
use Hephaestus\Testing\CommandRunner;

test('greets user', function () {
    CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'John'])
        ->run()
        ->assertSuccessful()
        ->assertOutputEquals('Hello, John!');
});

test('shouts when yell option is set', function () {
    CommandRunner::for(GreetCommand::class)
        ->withArgs(['name' => 'John'])
        ->withOptions(['yell' => true])
        ->run()
        ->assertOutputEquals('HELLO, JOHN!');
});

test('fails when name argument is missing', function () {
    CommandRunner::for(GreetCommand::class)
        ->run()
        ->assertFailed()
        ->assertExitCode(1)
        ->assertErrorOutputContains('missing: "name"');
});
```

---

## Why "Hephaestus"?

Hephaestus (Ἥφαιστος) is the Greek god of fire, metalworking, and the forge — the divine craftsman
who built weapons and tools for gods and heroes alike.

This library follows the same principle: hand developers the right tools so they can forge great
things without wasting time on the tedious parts.

🔨 **Forge. Build. Ship.**

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for setup instructions, code conventions, and PR guidelines.

---

## License

[MIT License](LICENSE)

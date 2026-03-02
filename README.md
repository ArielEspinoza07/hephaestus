# Hephaestus

> Forge powerful CLI tools with ease

A modern, type-safe wrapper around Symfony Console — write less boilerplate, get more type safety.

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

---

## Requirements

- PHP 8.5+
- Symfony Console 8.0+

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

| Attribute | Required | Description |
|-----------|----------|-------------|
| `#[Signature('app:name')]` | ✅ | CLI command name |
| `#[Description('...')]` | | Short description shown in the command list |
| `#[Help('...')]` | | Extended help shown by `help <command>` |
| `#[Usage(['app:name arg1', '...'])]` | | Example usages shown in help output |
| `#[Alias('alias1\|alias2')]` | | Pipe-separated command aliases |
| `#[Input]` | | Inject `InputInterface` into `$this->input` |
| `#[Output]` | | Inject `OutputInterface` into `$this->output` |

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
// Scan a directory and register all commands
->registerCommands(string $directory, ?string $cachePath = null): self

// Write parsed metadata to a cache file for faster startup
->registerCommands(__DIR__ . '/src/Commands', __DIR__ . '/var/cache/commands.cache')

// Run the CLI application
->run(): int
```

`registerCommands()` returns `$this` for fluent chaining and can be called multiple times for multiple directories.

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

Hephaestus (Ἥφαιστος) — Greek god of craftsmen, metalworking, and forging.

Just as Hephaestus forged legendary tools for the gods, this library helps you forge powerful CLI tools.

🔨 **Forge. Build. Ship.**

---

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## License

[MIT License](LICENSE)

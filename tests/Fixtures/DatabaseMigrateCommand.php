<?php

declare(strict_types=1);

namespace Hephaestus\Tests\Fixtures;

use Hephaestus\Attributes\Alias;
use Hephaestus\Attributes\Argument;
use Hephaestus\Attributes\Description;
use Hephaestus\Attributes\Option;
use Hephaestus\Attributes\Signature;
use Hephaestus\Attributes\Style;
use Hephaestus\Console\Command;

#[Signature('app:database-migrate')]
#[Description('Simulate database migration')]
#[Alias('app:database-migrate|app:db-migrate')]
#[Style]
final readonly class DatabaseMigrateCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param list<string> $columns
     */
    public function execute(
        #[Argument(description: 'Database name')]
        string $database,
        #[Argument(description: 'Specific table to migrate', required: false)]
        ?string $table = null,
        #[Option(description: 'Seed data after migration', shortcut: 's')]
        bool $seed = false,
        #[Option(description: 'Force migration without confirmation', shortcut: 'f')]
        bool $force = false,
        #[Argument(description: 'Specific columns to migrate')]
        array $columns = [],
    ): int {
        // Display header
        $this->consoleIO->output->info('=== Database Migration ===');
        $this->consoleIO->output->newLine();

        // Show configuration
        $this->consoleIO->output->writeln("Database: {$database}");

        if ($table) {
            $this->consoleIO->output->writeln("Target table: {$table}");
        } else {
            $this->consoleIO->output->comment('Migrating all tables');
        }

        if (! empty($columns)) {
            $this->consoleIO->output->writeln('Specific columns: '.implode(', ', $columns));
        }

        $this->consoleIO->output->newLine();

        // Show options
        $options = [];
        if ($seed) {
            $options[] = 'Seed data';
        }
        if ($force) {
            $options[] = 'Force mode';
        }

        if (! empty($options)) {
            $this->consoleIO->output->warning('Options: '.implode(', ', $options));
            $this->consoleIO->output->newLine();
        }

        // Simulate migration steps
        $this->consoleIO->output->info('Running migrations...');
        $this->consoleIO->output->newLine();

        // Show migration progress in a table
        $migrations = [
            ['create_users_table', '2024-01-01', 'completed'],
            ['create_posts_table', '2024-01-02', 'completed'],
            ['add_email_to_users', '2024-01-03', 'completed'],
        ];

        $this->consoleIO->output->table(
            ['Migration', 'Date', 'Status'],
            $migrations
        );

        $this->consoleIO->output->newLine();

        // Handle seeding if requested
        if ($seed) {
            $this->consoleIO->output->info('Seeding database...');
            $this->consoleIO->output->writeln('  ✓ Users seeded');
            $this->consoleIO->output->writeln('  ✓ Posts seeded');
            $this->consoleIO->output->newLine();
        }

        // Display summary
        $this->consoleIO->output->success('Migration completed successfully!');

        return self::SUCCESS;
    }
}

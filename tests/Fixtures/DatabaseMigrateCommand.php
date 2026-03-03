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
        $this->output->info('=== Database Migration ===');
        $this->output->newLine();

        // Show configuration
        $this->output->writeln("Database: {$database}");

        if ($table) {
            $this->output->writeln("Target table: {$table}");
        } else {
            $this->output->comment('Migrating all tables');
        }

        if (! empty($columns)) {
            $this->output->writeln('Specific columns: '.implode(', ', $columns));
        }

        $this->output->newLine();

        // Show options
        $options = [];
        if ($seed) {
            $options[] = 'Seed data';
        }
        if ($force) {
            $options[] = 'Force mode';
        }

        if (! empty($options)) {
            $this->output->warning('Options: '.implode(', ', $options));
            $this->output->newLine();
        }

        // Simulate migration steps
        $this->output->info('Running migrations...');
        $this->output->newLine();

        // Show migration progress in a table
        $migrations = [
            ['create_users_table', '2024-01-01', 'completed'],
            ['create_posts_table', '2024-01-02', 'completed'],
            ['add_email_to_users', '2024-01-03', 'completed'],
        ];

        $this->output->table(
            ['Migration', 'Date', 'Status'],
            $migrations
        );

        $this->output->newLine();

        // Handle seeding if requested
        if ($seed) {
            $this->output->info('Seeding database...');
            $this->output->writeln('  ✓ Users seeded');
            $this->output->writeln('  ✓ Posts seeded');
            $this->output->newLine();
        }

        // Display summary
        $this->output->success('Migration completed successfully!');

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

class Migration
{
    private Database $db;
    private string $migrationsPath;
    private string $migrationsTable = 'migrations';

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationsPath = dirname(__DIR__) . '/Migrations';
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable(): void
    {
        $this->db->statement("
            CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function run(): array
    {
        $files = $this->getMigrationFiles();
        $ran = $this->getRanMigrations();
        $pending = array_diff($files, $ran);

        if (empty($pending)) {
            return ['message' => 'Nothing to migrate.', 'migrations' => []];
        }

        $batch = $this->getNextBatch();
        $migrated = [];

        foreach ($pending as $file) {
            $this->runMigration($file, $batch);
            $migrated[] = $file;
        }

        return ['message' => count($migrated) . ' migration(s) executed.', 'migrations' => $migrated];
    }

    public function rollback(): array
    {
        $batch = $this->getLastBatch();
        if ($batch === 0) {
            return ['message' => 'Nothing to rollback.', 'migrations' => []];
        }

        $migrations = $this->db->table($this->migrationsTable)
            ->where('batch', $batch)
            ->orderBy('id', 'DESC')
            ->get();

        $rolledBack = [];

        foreach ($migrations as $migration) {
            $this->rollbackMigration($migration['migration']);
            $rolledBack[] = $migration['migration'];
        }

        return ['message' => count($rolledBack) . ' migration(s) rolled back.', 'migrations' => $rolledBack];
    }

    public function reset(): array
    {
        $migrations = $this->db->table($this->migrationsTable)
            ->orderBy('id', 'DESC')
            ->get();

        $rolledBack = [];

        foreach ($migrations as $migration) {
            $this->rollbackMigration($migration['migration']);
            $rolledBack[] = $migration['migration'];
        }

        return ['message' => count($rolledBack) . ' migration(s) rolled back.', 'migrations' => $rolledBack];
    }

    public function fresh(): array
    {
        // Drop all tables
        $this->db->statement('SET FOREIGN_KEY_CHECKS = 0');
        $tables = $this->db->raw("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $this->db->statement("DROP TABLE IF EXISTS `{$table}`");
        }
        $this->db->statement('SET FOREIGN_KEY_CHECKS = 1');

        // Re-create migrations table and run all
        $this->ensureMigrationsTable();
        return $this->run();
    }

    public function status(): array
    {
        $files = $this->getMigrationFiles();
        $ran = $this->getRanMigrations();
        $status = [];

        foreach ($files as $file) {
            $status[] = [
                'migration' => $file,
                'status' => in_array($file, $ran) ? 'Ran' : 'Pending',
            ];
        }

        return $status;
    }

    private function runMigration(string $file, int $batch): void
    {
        $path = $this->migrationsPath . '/' . $file;
        $migration = require $path;

        if (isset($migration['up']) && is_callable($migration['up'])) {
            $migration['up']($this->db);
        }

        $this->db->table($this->migrationsTable)->insert([
            'migration' => $file,
            'batch' => $batch,
        ]);
    }

    private function rollbackMigration(string $file): void
    {
        $path = $this->migrationsPath . '/' . $file;

        if (file_exists($path)) {
            $migration = require $path;
            if (isset($migration['down']) && is_callable($migration['down'])) {
                $migration['down']($this->db);
            }
        }

        $this->db->table($this->migrationsTable)
            ->where('migration', $file)
            ->delete();
    }

    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) return [];

        $files = scandir($this->migrationsPath);
        $migrations = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $migrations[] = $file;
            }
        }

        sort($migrations);
        return $migrations;
    }

    private function getRanMigrations(): array
    {
        return array_column(
            $this->db->table($this->migrationsTable)->select('migration')->get(),
            'migration'
        );
    }

    private function getNextBatch(): int
    {
        return $this->getLastBatch() + 1;
    }

    private function getLastBatch(): int
    {
        $result = $this->db->table($this->migrationsTable)
            ->select('MAX(batch) as batch')
            ->first();
        return (int) ($result['batch'] ?? 0);
    }
}

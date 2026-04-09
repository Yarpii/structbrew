<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private string $prefix;

    private ?string $table = null;
    private array $wheres = [];
    private array $bindings = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private array $selects = ['*'];

    private function __construct()
    {
        $config = Config::get('database', []);

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $name = $config['name'] ?? 'structbrew';
        $user = $config['user'] ?? 'root';
        $pass = $config['pass'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $this->prefix = $config['prefix'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}",
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    public static function connect(): self
    {
        return self::getInstance();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    // ─── Query Builder ───────────────────────────────────────

    public function table(string $table): self
    {
        $builder = clone $this;
        $builder->reset();
        $builder->table = $this->prefix . $table;
        return $builder;
    }

    private function reset(): void
    {
        $this->wheres = [];
        $this->bindings = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->joins = [];
        $this->selects = ['*'];
    }

    public function select(string ...$columns): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, mixed $operatorOrValue, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operatorOrValue;
            $operator = '=';
        } else {
            $operator = $operatorOrValue;
        }

        $placeholder = ':w_' . str_replace('.', '_', $column) . '_' . count($this->wheres);
        $this->wheres[] = "{$column} {$operator} {$placeholder}";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = [];
        foreach ($values as $i => $val) {
            $key = ':win_' . count($this->wheres) . "_{$i}";
            $placeholders[] = $key;
            $this->bindings[$key] = $val;
        }
        $this->wheres[] = "{$column} IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }

    public function whereRaw(string $sql, array $bindings = []): self
    {
        $this->wheres[] = $sql;
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $table = $this->prefix . $table;
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    // ─── Execute Queries ─────────────────────────────────────

    public function get(): array
    {
        $sql = $this->buildSelect();
        return $this->query($sql, $this->bindings)->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit = 1;
        $sql = $this->buildSelect();
        $result = $this->query($sql, $this->bindings)->fetch();
        return $result ?: null;
    }

    public function count(string $column = '*'): int
    {
        $this->selects = ["COUNT({$column}) as aggregate"];
        $sql = $this->buildSelect();
        $result = $this->query($sql, $this->bindings)->fetch();
        return (int) ($result['aggregate'] ?? 0);
    }

    public function sum(string $column): float
    {
        $this->selects = ["SUM({$column}) as aggregate"];
        $sql = $this->buildSelect();
        $result = $this->query($sql, $this->bindings)->fetch();
        return (float) ($result['aggregate'] ?? 0);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $total = (clone $this)->count();
        $this->limit = $perPage;
        $this->offset = ($page - 1) * $perPage;
        $items = $this->get();
        $lastPage = (int) ceil($total / $perPage);

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'from' => ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
        ];
    }

    public function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($k) => ":{$k}", array_keys($data)));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $bindings = [];
        foreach ($data as $key => $value) {
            $bindings[":{$key}"] = $value;
        }
        $this->query($sql, $bindings);
        return (int) $this->pdo->lastInsertId();
    }

    public function insertBatch(array $rows): int
    {
        if (empty($rows)) return 0;

        $columns = array_keys($rows[0]);
        $colStr = implode(', ', $columns);
        $placeholderSets = [];
        $bindings = [];

        foreach ($rows as $i => $row) {
            $set = [];
            foreach ($columns as $col) {
                $key = ":{$col}_{$i}";
                $set[] = $key;
                $bindings[$key] = $row[$col] ?? null;
            }
            $placeholderSets[] = '(' . implode(', ', $set) . ')';
        }

        $sql = "INSERT INTO {$this->table} ({$colStr}) VALUES " . implode(', ', $placeholderSets);
        $stmt = $this->query($sql, $bindings);
        return $stmt->rowCount();
    }

    public function update(array $data): int
    {
        $sets = [];
        $bindings = [];
        foreach ($data as $key => $value) {
            $placeholder = ":set_{$key}";
            $sets[] = "{$key} = {$placeholder}";
            $bindings[$placeholder] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        $sql .= $this->buildWhere();
        $bindings = array_merge($bindings, $this->bindings);

        return $this->query($sql, $bindings)->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}" . $this->buildWhere();
        return $this->query($sql, $this->bindings)->rowCount();
    }

    // ─── Raw Queries ─────────────────────────────────────────

    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    public function raw(string $sql, array $bindings = []): PDOStatement
    {
        return $this->query($sql, $bindings);
    }

    // ─── Transactions ────────────────────────────────────────

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    // ─── Schema Methods ──────────────────────────────────────

    public function statement(string $sql): bool
    {
        return $this->pdo->exec($sql) !== false;
    }

    public function tableExists(string $table): bool
    {
        $table = $this->prefix . $table;
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    }

    // ─── SQL Builders ────────────────────────────────────────

    private function buildSelect(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";
        $sql .= $this->buildJoins();
        $sql .= $this->buildWhere();
        $sql .= $this->buildGroupBy();
        $sql .= $this->buildOrderBy();
        $sql .= $this->buildLimit();
        return $sql;
    }

    private function buildJoins(): string
    {
        return empty($this->joins) ? '' : ' ' . implode(' ', $this->joins);
    }

    private function buildWhere(): string
    {
        return empty($this->wheres) ? '' : ' WHERE ' . implode(' AND ', $this->wheres);
    }

    private function buildGroupBy(): string
    {
        return empty($this->groupBy) ? '' : ' GROUP BY ' . implode(', ', $this->groupBy);
    }

    private function buildOrderBy(): string
    {
        return empty($this->orderBy) ? '' : ' ORDER BY ' . implode(', ', $this->orderBy);
    }

    private function buildLimit(): string
    {
        $sql = '';
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        return $sql;
    }

    public function __clone()
    {
        // Keep same PDO connection but allow independent query building
    }
}

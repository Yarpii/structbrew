<?php

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static array $hidden = [];
    protected static array $casts = [];

    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    // ─── Static Query Methods ────────────────────────────────

    protected static function db(): Database
    {
        return Database::getInstance();
    }

    protected static function query(): Database
    {
        return static::db()->table(static::$table);
    }

    public static function find(int|string $id): ?static
    {
        $row = static::query()->where(static::$primaryKey, $id)->first();
        return $row ? static::hydrate($row) : null;
    }

    public static function findOrFail(int|string $id): static
    {
        $model = static::find($id);
        if (!$model) {
            throw new \RuntimeException(static::class . " with ID {$id} not found.");
        }
        return $model;
    }

    public static function all(array $columns = ['*']): array
    {
        $rows = static::query()->select(...$columns)->get();
        return array_map(fn($row) => static::hydrate($row), $rows);
    }

    public static function where(string $column, mixed $operatorOrValue, mixed $value = null): Database
    {
        return static::query()->where($column, $operatorOrValue, $value);
    }

    public static function create(array $data): static
    {
        $filtered = static::filterFillable($data);
        $filtered['created_at'] = $filtered['created_at'] ?? date('Y-m-d H:i:s');
        $filtered['updated_at'] = $filtered['updated_at'] ?? date('Y-m-d H:i:s');

        $id = static::query()->insert($filtered);
        return static::find($id);
    }

    public static function count(): int
    {
        return static::query()->count();
    }

    public static function paginate(int $perPage = 15, int $page = 1): array
    {
        $result = static::query()->paginate($perPage, $page);
        $result['data'] = array_map(fn($row) => static::hydrate($row), $result['data']);
        return $result;
    }

    public static function destroy(int|string ...$ids): int
    {
        return static::query()->whereIn(static::$primaryKey, $ids)->delete();
    }

    // ─── Instance Methods ────────────────────────────────────

    public function save(): bool
    {
        $data = static::filterFillable($this->attributes);
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->exists) {
            $affected = static::query()
                ->where(static::$primaryKey, $this->getId())
                ->update($data);
            return $affected > 0;
        }

        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $id = static::query()->insert($data);
        $this->attributes[static::$primaryKey] = $id;
        $this->exists = true;
        return true;
    }

    public function update(array $data): bool
    {
        $this->fill($data);
        return $this->save();
    }

    public function delete(): bool
    {
        if (!$this->exists) return false;

        $affected = static::query()
            ->where(static::$primaryKey, $this->getId())
            ->delete();

        $this->exists = false;
        return $affected > 0;
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function getId(): mixed
    {
        return $this->attributes[static::$primaryKey] ?? null;
    }

    public function toArray(): array
    {
        return array_diff_key($this->attributes, array_flip(static::$hidden));
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    // ─── Attribute Access ────────────────────────────────────

    public function __get(string $name): mixed
    {
        return $this->getAttribute($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function getAttribute(string $name): mixed
    {
        $value = $this->attributes[$name] ?? null;

        // Check for cast
        if (isset(static::$casts[$name]) && $value !== null) {
            return match (static::$casts[$name]) {
                'int', 'integer' => (int) $value,
                'float', 'double' => (float) $value,
                'bool', 'boolean' => (bool) $value,
                'string' => (string) $value,
                'array', 'json' => is_string($value) ? json_decode($value, true) : $value,
                default => $value,
            };
        }

        return $value;
    }

    // ─── Relationships ───────────────────────────────────────

    protected function hasMany(string $related, string $foreignKey, ?string $localKey = null): array
    {
        $localKey = $localKey ?? static::$primaryKey;
        /** @var Model $related */
        return $related::query()
            ->where($foreignKey, $this->getAttribute($localKey))
            ->get();
    }

    protected function belongsTo(string $related, string $foreignKey, ?string $ownerKey = null): ?array
    {
        $ownerKey = $ownerKey ?? 'id';
        /** @var Model $related */
        return $related::query()
            ->where($ownerKey, $this->getAttribute($foreignKey))
            ->first();
    }

    // ─── Helpers ─────────────────────────────────────────────

    protected static function hydrate(array $row): static
    {
        $model = new static($row);
        $model->exists = true;
        $model->original = $row;
        return $model;
    }

    protected static function filterFillable(array $data): array
    {
        if (empty(static::$fillable)) return $data;
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    public function isDirty(): bool
    {
        return $this->attributes !== $this->original;
    }

    public static function generateSlug(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}

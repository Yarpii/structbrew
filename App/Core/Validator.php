<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;
    private array $rules;

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'rule' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $params);
                }
            }
        }

        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function validated(): array
    {
        $validated = [];
        foreach (array_keys($this->rules) as $field) {
            if (array_key_exists($field, $this->data)) {
                $validated[$field] = $this->data[$field];
            }
        }
        return $validated;
    }

    // ─── Validation Rules ────────────────────────────────────

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function ruleRequired(string $field, mixed $value, array $params): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, "{$field} is required.");
        }
    }

    private function ruleEmail(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$field} must be a valid email address.");
        }
    }

    private function ruleMin(string $field, mixed $value, array $params): void
    {
        $min = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) < $min) {
            $this->addError($field, "{$field} must be at least {$min} characters.");
        }
        if (is_numeric($value) && $value < $min) {
            $this->addError($field, "{$field} must be at least {$min}.");
        }
    }

    private function ruleMax(string $field, mixed $value, array $params): void
    {
        $max = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) > $max) {
            $this->addError($field, "{$field} must not exceed {$max} characters.");
        }
        if (is_numeric($value) && $value > $max) {
            $this->addError($field, "{$field} must not exceed {$max}.");
        }
    }

    private function ruleNumeric(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, "{$field} must be numeric.");
        }
    }

    private function ruleInteger(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, "{$field} must be an integer.");
        }
    }

    private function ruleString(string $field, mixed $value, array $params): void
    {
        if ($value !== null && !is_string($value)) {
            $this->addError($field, "{$field} must be a string.");
        }
    }

    private function ruleUrl(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "{$field} must be a valid URL.");
        }
    }

    private function ruleUnique(string $field, mixed $value, array $params): void
    {
        if ($value === null || $value === '') return;

        $table = $params[0] ?? $field;
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;

        $query = Database::getInstance()->table($table)->where($column, $value);
        if ($exceptId) {
            $query = $query->where('id', '!=', $exceptId);
        }

        if ($query->exists()) {
            $this->addError($field, "{$field} is already taken.");
        }
    }

    private function ruleConfirmed(string $field, mixed $value, array $params): void
    {
        $confirmField = $field . '_confirmation';
        if ($value !== ($this->data[$confirmField] ?? null)) {
            $this->addError($field, "{$field} confirmation does not match.");
        }
    }

    private function ruleIn(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !in_array($value, $params)) {
            $this->addError($field, "{$field} must be one of: " . implode(', ', $params));
        }
    }

    private function ruleSlug(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            $this->addError($field, "{$field} must be a valid slug (lowercase, hyphens only).");
        }
    }

    private function ruleJson(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '') {
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError($field, "{$field} must be valid JSON.");
            }
        }
    }

    private function ruleDecimal(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !preg_match('/^\d+(\.\d{1,2})?$/', (string) $value)) {
            $this->addError($field, "{$field} must be a valid decimal number.");
        }
    }

    private function ruleImage(string $field, mixed $value, array $params): void
    {
        if (!is_array($value) || empty($value['tmp_name'])) return;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $value['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {
            $this->addError($field, "{$field} must be an image (jpg, png, gif, webp, svg).");
        }
    }
}

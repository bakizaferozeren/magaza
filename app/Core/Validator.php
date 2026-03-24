<?php

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    private function validate(array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

        $label = $this->fieldLabel($field);

        match($ruleName) {
            'required' => (empty($value) && $value !== '0')
                ? $this->addError($field, "{$label} alani zorunludur") : null,

            'email' => (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL))
                ? $this->addError($field, "Gecerli bir e-posta giriniz") : null,

            'min' => (!empty($value) && mb_strlen((string)$value) < (int)$param)
                ? $this->addError($field, "{$label} en az {$param} karakter olmalidir") : null,

            'max' => (!empty($value) && mb_strlen((string)$value) > (int)$param)
                ? $this->addError($field, "{$label} en fazla {$param} karakter olmalidir") : null,

            'numeric' => (!empty($value) && !is_numeric($value))
                ? $this->addError($field, "{$label} sayisal olmalidir") : null,

            'integer' => (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT))
                ? $this->addError($field, "{$label} tam sayi olmalidir") : null,

            'url' => (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL))
                ? $this->addError($field, "Gecerli bir URL giriniz") : null,

            'unique' => $this->validateUnique($field, $value, $param),

            'confirmed' => (isset($this->data[$field . '_confirmation'])
                && $value !== $this->data[$field . '_confirmation'])
                ? $this->addError($field, "{$label} eslesmiyor") : null,

            'in' => (!empty($value) && !in_array($value, explode(',', $param)))
                ? $this->addError($field, "Gecersiz {$label} degeri") : null,

            'phone' => (!empty($value) && !preg_match('/^(\+90|0)?[0-9]{10}$/', preg_replace('/\s/', '', $value)))
                ? $this->addError($field, "Gecerli bir telefon numarasi giriniz") : null,

            default => null,
        };
    }

    private function validateUnique(string $field, mixed $value, ?string $param): void
    {
        if (empty($value) || !$param) return;

        // Format: table,column,ignore_id
        $parts = explode(',', $param);
        $table  = $parts[0];
        $column = $parts[1] ?? $field;
        $ignore = $parts[2] ?? null;

        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
        $params = [$value];

        if ($ignore) {
            $sql .= " AND id != ?";
            $params[] = $ignore;
        }

        $count = (int) Database::value($sql, $params);

        if ($count > 0) {
            $this->addError($field, $this->fieldLabel($field) . ' zaten kullaniliyor');
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function fieldLabel(string $field): string
    {
        $labels = [
            'email'     => 'E-posta',
            'password'  => 'Sifre',
            'name'      => 'Ad',
            'surname'   => 'Soyad',
            'phone'     => 'Telefon',
            'title'     => 'Baslik',
            'price'     => 'Fiyat',
            'stock'     => 'Stok',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
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

    public function allErrors(): array
    {
        $all = [];
        foreach ($this->errors as $messages) {
            $all = array_merge($all, $messages);
        }
        return $all;
    }
}

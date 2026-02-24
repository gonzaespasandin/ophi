<?php

namespace App\Classifier\Strategies;

class KeyWordStrategy
{
    protected array $rules = [];
    protected ?string $value = null;

    public static function rules(array $rules): static
    {
        $instance = new static();
        $instance->rules = $rules;

        return $instance;
    }

    public function against(string $value): static
    {
        $this->value = $this->normalize($value);

        return $this;
    }

    public function run(): bool
    {
        if (!$this->value) {
            throw new \RuntimeException('No value provided. Use ->against($value)');
        }

        foreach ($this->rules as $rule) {
            if ($this->evaluateRule($rule)) {
                return true; // OR lógico entre reglas
            }
        }

        return false;
    }

    protected function evaluateRule(string $rule): bool
    {
        $conditions = explode('|', $rule);

        foreach ($conditions as $condition) {
            $condition = trim($condition);

            $isNegated = str_starts_with($condition, '!');
            $term = $isNegated
                ? substr($condition, 1)
                : $condition;

            $term = $this->normalize($term);

            $contains = str_contains($this->value, $term);

            if ($isNegated && $contains) {
                return false;
            }

            if (!$isNegated && !$contains) {
                return false;
            }
        }

        return true; // todas las condiciones pasaron
    }

    protected function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^a-z0-9\s\-]/', '', $text);

        return trim($text);
    }
}

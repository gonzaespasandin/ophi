<?php

namespace App\Classifier\Strategies;

class ExactMatchStrategy
{
    protected array $words = [];
    protected array $tokens = [];

    public static function words(array $words): static
    {
        $instance = new static();

        $instance->words = array_map(
            fn($word) => $instance->normalize($word),
            $words
        );

        return $instance;
    }

    public function against(string $value): static
    {
        $normalized = $this->normalize($value);

        // Split por espacios múltiples
        $this->tokens = preg_split('/\s+/', $normalized);

        return $this;
    }

    public function run(): bool
    {
        if (empty($this->tokens)) {
            return false;
            // throw new \RuntimeException('No value provided. Use ->against($value)');
        }

        foreach ($this->words as $word) {
            if (in_array($word, $this->tokens, true)) {
                return true;
            }
        }

        return false;
    }

    protected function normalize(string $text): string
    {
        return trim($text);
    }
}

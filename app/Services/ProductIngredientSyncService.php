<?php

namespace App\Services;

use App\Classifier\IngredientClassifier;
use App\Models\Ingredient;
use App\Models\Product;

class ProductIngredientSyncService
{
    /**
     * @param array<int, array<string, mixed>|string> $ingredients
     * @return array<int, int>
     */
    public function sync(Product $product, array $ingredients): array
    {
        $syncPayload = [];

        foreach ($this->normalizeItems($ingredients) as $item) {
            $name = $item['name'];
            $ingredient = Ingredient::where('name', $name)->first();

            if (! $ingredient) {
                $ingredient = new Ingredient();
                $ingredient->name = $name;
                $ingredient->save();

                IngredientClassifier::runOne($ingredient);
            }

            $currentIsTrace = $syncPayload[$ingredient->id]['is_trace'] ?? true;
            $syncPayload[$ingredient->id] = [
                // If the same ingredient appears as declared and as trace, declared wins.
                'is_trace' => $currentIsTrace && $item['is_trace'],
            ];
        }

        $product->ingredients()->sync($syncPayload);

        return array_map('intval', array_keys($syncPayload));
    }

    /**
     * @param array<int, array<string, mixed>|string> $items
     * @return array<int, array{name: string, is_trace: bool}>
     */
    private function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            foreach ($this->ingredientCandidates($item) as $candidate) {
                $name = Ingredient::normalize($candidate['name']);
                $name = trim($name, " \t\n\r\0\x0B'\"[],.;:-");
                $name = preg_replace('/\s+/', ' ', $name);

                if ($name === '' || $name === 'y' || $name === 'e') {
                    continue;
                }

                if (mb_strlen($name) > 100) {
                    $name = trim(mb_substr($name, 0, 100));
                }

                if (! isset($normalized[$name])) {
                    $normalized[$name] = [
                        'name' => $name,
                        'is_trace' => $candidate['is_trace'],
                    ];
                    continue;
                }

                $normalized[$name]['is_trace'] = $normalized[$name]['is_trace'] && $candidate['is_trace'];
            }
        }

        return array_values($normalized);
    }

    /**
     * @param array<string, mixed>|string $item
     * @return array<int, array{name: string, is_trace: bool}>
     */
    private function ingredientCandidates(array|string $item): array
    {
        if (is_array($item)) {
            $name = (string) ($item['nombre'] ?? $item['name'] ?? $item['ingredient'] ?? '');
            $ins = (string) ($item['ins'] ?? '');

            if (trim($name) === '' && trim($ins) !== '') {
                $name = 'ins ' . $ins;
            }

            return $this->splitIngredientValue(
                $name,
                $this->isTraceValue($item)
            );
        }

        return $this->splitIngredientValue($item, false);
    }

    /**
     * @return array<int, array{name: string, is_trace: bool}>
     */
    private function splitIngredientValue(string $value, bool $isTrace): array
    {
        $value = trim($value);
        $value = trim($value, " \t\n\r\0\x0B[]");

        if ($value === '') {
            return [];
        }

        $traceOffset = $this->traceMarkerOffset($value);
        if ($traceOffset !== null) {
            $before = trim(substr($value, 0, $traceOffset['offset']));
            $after = trim(substr($value, $traceOffset['offset'] + $traceOffset['length']));

            return [
                ...$this->splitIngredientValue($before, $isTrace),
                ...$this->splitList($after, true),
            ];
        }

        if (str_contains($value, "','") || str_contains($value, "', '")) {
            return $this->wrapCandidates(preg_split("/'\\s*,\\s*'/", trim($value, "'")) ?: [], $isTrace);
        }

        if (str_contains($value, '","') || str_contains($value, '", "')) {
            return $this->wrapCandidates(preg_split('/"\\s*,\\s*"/', trim($value, '"')) ?: [], $isTrace);
        }

        if (str_contains($value, ',') || str_contains($value, ';')) {
            return $this->splitList($value, $isTrace);
        }

        return [['name' => $value, 'is_trace' => $isTrace]];
    }

    /**
     * @return array<int, array{name: string, is_trace: bool}>
     */
    private function splitList(string $value, bool $isTrace): array
    {
        $parts = preg_split('/[,;]+/', $value) ?: [];

        if ($isTrace) {
            $parts = collect($parts)
                ->flatMap(fn (string $part) => preg_split('/\s+(?:y|e|o)\s+/iu', $part) ?: [])
                ->all();
        }

        return $this->wrapCandidates($parts, $isTrace);
    }

    /**
     * @param array<int, string> $names
     * @return array<int, array{name: string, is_trace: bool}>
     */
    private function wrapCandidates(array $names, bool $isTrace): array
    {
        return collect($names)
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->map(fn (string $name) => ['name' => $name, 'is_trace' => $isTrace])
            ->values()
            ->all();
    }

    /**
     * @return array{offset: int, length: int}|null
     */
    private function traceMarkerOffset(string $value): ?array
    {
        $patterns = [
            '/\bpuede\s+contener\s+trazas?\s+de\b/iu',
            '/\bpuede\s+contener\b/iu',
            '/\bcontiene\s+trazas?\s+de\b/iu',
            '/\btrazas?\s+de\b/iu',
            '/\bmay\s+contain\s+traces?\s+of\b/iu',
            '/\bmay\s+contain\b/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
                return [
                    'offset' => $matches[0][1],
                    'length' => strlen($matches[0][0]),
                ];
            }
        }

        return null;
    }

    /**
     * Accept common OCR misspellings while keeping the canonical stored field as is_trace.
     */
    private function isTraceValue(array $item): bool
    {
        foreach (['is_trace', 'trace', 'isTrace', 'ins_trace', 'traze', 'is_trae', 'es_trace'] as $key) {
            if (array_key_exists($key, $item) && filter_var($item[$key], FILTER_VALIDATE_BOOLEAN)) {
                return true;
            }
        }

        return false;
    }
}

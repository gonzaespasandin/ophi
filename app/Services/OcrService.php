<?php

namespace App\Services;

use App\Exceptions\OcrConfigurationException;
use App\Models\InsCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use OpenAI;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Facades\OpenAI as OpenAIFacade;

class OcrService
{
    /**
     * Extrae y normaliza la lista de ingredientes a partir de una foto de packaging.
     *
     * @return array<int, array{nombre: string, ins: string|null, is_trace: bool}>
     */
    public function extractIngredientsFromImage(UploadedFile $image): array
    {
        $this->ensureProviderIsConfigured();

        $rawItems = match ($this->provider()) {
            'ollama_service' => $this->extractWithOllamaService($image),
            'ollama' => $this->extractWithOllama(
                base64_encode(file_get_contents($image->getRealPath()))
            ),
            default => $this->extractWithOpenAi(
                base64_encode(file_get_contents($image->getRealPath())),
                $image->getMimeType()
            ),
        };

        return $this->resolveItems($rawItems);
    }

    /**
     * @return array<int, array<string, mixed>|string>
     */
    protected function extractWithOllamaService(UploadedFile $image): array
    {
        $response = Http::timeout((int) config('openai.ocr_service.timeout', 240))
            ->acceptJson()
            ->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName() ?: 'ingredients.jpg'
            )
            ->post($this->ocrServiceExtractUrl(), [
                'preprocess' => config('openai.ocr_service.preprocess', true) ? 'true' : 'false',
                'language' => config('openai.ocr_service.language', 'Spanish'),
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(sprintf(
                'Ollama OCR service fallo con HTTP %s: %s',
                $response->status(),
                mb_substr($response->body(), 0, 500)
            ));
        }

        $items = $response->json('ingredientes', []);

        return is_array($items) ? $items : [];
    }

    /**
     * @return array<int, array<string, mixed>|string>
     */
    protected function extractWithOpenAi(string $base64, string $mimeType): array
    {
        $payload = [
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64}",
                                'detail' => 'high',
                            ],
                        ],
                        ['type' => 'text', 'text' => 'Extrae los ingredientes de esta foto de packaging.'],
                    ],
                ],
            ],
        ];

        $payload['model'] = $this->provider() === 'azure'
            ? config('openai.azure.deployment')
            : config('openai.ocr_model', 'gpt-4o-mini');

        $response = $this->client()->chat()->create($payload);
        $content = $response->choices[0]->message->content ?? '{}';
        $decoded = $this->decodeJsonObject($content);

        return is_array($decoded['ingredientes'] ?? null) ? $decoded['ingredientes'] : [];
    }

    /**
     * @return array<int, array<string, mixed>|string>
     */
    protected function extractWithOllama(string $base64): array
    {
        $response = Http::timeout((int) config('openai.ollama.timeout', 180))
            ->acceptJson()
            ->asJson()
            ->post($this->ollamaGenerateUrl(), [
                'model' => config('openai.ollama.model', 'llama3.2-vision:11b'),
                'prompt' => $this->ollamaPrompt(),
                'stream' => false,
                'format' => 'json',
            'images' => [$base64],
            'options' => [
                'temperature' => 0,
                'num_predict' => (int) config('openai.ollama.num_predict', 1400),
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(sprintf(
                'Ollama OCR fallo con HTTP %s: %s',
                $response->status(),
                mb_substr($response->body(), 0, 500)
            ));
        }

        $content = $response->json('response', '{}');
        $decoded = $this->decodeJsonObject(is_string($content) ? $content : '{}');

        return is_array($decoded['ingredientes'] ?? null) ? $decoded['ingredientes'] : [];
    }

    protected function ollamaGenerateUrl(): string
    {
        return rtrim((string) config('openai.ollama.base_url', 'http://host.docker.internal:11434'), '/')
            . '/api/generate';
    }

    protected function ocrServiceExtractUrl(): string
    {
        return rtrim((string) config('openai.ocr_service.url', 'http://ocr-service:8000'), '/')
            . '/extract-ingredients';
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeJsonObject(string $content): array
    {
        $decoded = json_decode($content, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $decoded = json_decode($matches[0], true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Recibe el array de objetos {nombre, ins, is_trace} y devuelve items normalizados.
     *
     * @param array<int, array<string, mixed>|string> $items
     * @return array<int, array{nombre: string, ins: string|null, is_trace: bool}>
     */
    protected function resolveItems(array $items): array
    {
        if (empty($items)) {
            return [];
        }

        $codesNeeded = collect($items)
            ->filter(fn($item) => is_array($item) && empty($item['nombre'] ?? '') && ! empty($item['ins'] ?? ''))
            ->pluck('ins')
            ->map(fn($code) => $this->cleanIns($code))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $lookup = $codesNeeded
            ? InsCode::whereIn('code', $codesNeeded)->pluck('nombre', 'code')
            : collect();

        $result = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                $nombre = trim($item);
                $ins = '';
                $isTrace = $this->containsTraceMarker($nombre);
            } elseif (is_array($item)) {
                $nombre = trim((string) ($item['nombre'] ?? ''));
                $ins = $this->cleanIns($item['ins'] ?? '');
                $isTrace = $this->isTraceValue($item);

                if ($this->containsTraceMarker($nombre)) {
                    $isTrace = true;
                }
            } else {
                continue;
            }

            $nombre = $this->stripTraceMarker($nombre);
            $nombre = $this->stripContainsMarker($nombre);

            if ($nombre !== '' && $ins !== '') {
                $resolvedName = "{$nombre} (INS {$ins})";
            } elseif ($nombre !== '') {
                $resolvedName = $nombre;
            } elseif ($ins !== '') {
                $insName = $lookup[$ins] ?? null;
                $resolvedName = $insName !== null ? "{$insName} (INS {$ins})" : "ins {$ins}";
            } else {
                continue;
            }

            $result[] = [
                'nombre' => $resolvedName,
                'ins' => $ins !== '' ? $ins : null,
                'is_trace' => $isTrace,
            ];
        }

        return array_values($result);
    }

    protected function cleanIns(mixed $value): string
    {
        $ins = strtolower(trim((string) $value));

        if (in_array($ins, ['', 'null', 'none', 'nil', 'nan', 'n/a', 'na'], true)) {
            return '';
        }

        $ins = preg_replace('/^ins\s*/i', '', $ins) ?? $ins;
        $ins = preg_replace('/[^0-9a-z]/i', '', $ins) ?? $ins;

        if (in_array($ins, ['', 'null', 'none', 'nil', 'nan', 'na'], true)) {
            return '';
        }

        if (preg_match('/^\d{3}l$/', $ins) === 1) {
            return substr($ins, 0, -1) . 'i';
        }

        return $ins;
    }

    /**
     * Gemma occasionally misspells the trace key; keep those variants as traces
     * so "puede contener" never falls back into declared ingredients.
     */
    protected function isTraceValue(array $item): bool
    {
        foreach (['is_trace', 'trace', 'isTrace', 'ins_trace', 'traze', 'is_trae', 'es_trace'] as $key) {
            if (array_key_exists($key, $item) && filter_var($item[$key], FILTER_VALIDATE_BOOLEAN)) {
                return true;
            }
        }

        return false;
    }

    protected function containsTraceMarker(string $value): bool
    {
        return preg_match(
            '/\b(?:puede\s+contener(?:\s+trazas?\s+de)?|contiene\s+trazas?\s+de|trazas?\s+de|may\s+contain(?:\s+traces?\s+of)?)\b/iu',
            $value
        ) === 1;
    }

    protected function stripTraceMarker(string $value): string
    {
        $value = preg_replace(
            '/\b(?:puede\s+contener(?:\s+trazas?\s+de)?|contiene\s+trazas?\s+de|trazas?\s+de|may\s+contain(?:\s+traces?\s+of)?)\b/iu',
            '',
            $value
        ) ?? $value;

        return trim($value, " \t\n\r\0\x0B,.;:-");
    }

    protected function stripContainsMarker(string $value): string
    {
        if ($this->containsTraceMarker($value)) {
            return $value;
        }

        $value = preg_replace('/\bcontiene\b/iu', '', $value) ?? $value;

        return trim($value, " \t\n\r\0\x0B,.;:-");
    }

    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
Sos un asistente especializado en extraer listas de ingredientes de fotos de packaging de alimentos argentinos.

Formato de respuesta: devolve UNICAMENTE este JSON valido:
{
  "ingredientes": [
    { "nombre": "leche entera", "ins": null, "is_trace": false },
    { "nombre": "sorbato de potasio", "ins": "202", "is_trace": false },
    { "nombre": "mani", "ins": null, "is_trace": true },
    { "nombre": null, "ins": "160b", "is_trace": false }
  ]
}

Reglas para "nombre":
- Recorre toda la etiqueta de arriba hacia abajo. No te detengas en la primera linea: despues de aditivos suelen venir harinas, sales, vitaminas y advertencias.
- Minusculas, salvo nombres propios imprescindibles.
- Sin porcentajes ni valores numericos.
- Sin parentesis ni contenido secundario.
- Sin marcas comerciales ni nombres de empresa.
- Si aparece una frase descriptiva como "alimento a base de azucar, aceite vegetal y almidon sabor frutilla", no devuelvas la frase completa: separa sus componentes reales ("azucar", "aceite vegetal", "almidon", "aromatizante a frutilla").
- Inclui ingredientes despues de frases como "harina de trigo enriquecida segun Ley 25.630", "harina de maiz", "harina de avena", "salvado", "extracto de malta", "sal", "carbonato de calcio".
- Si aparece "CONTIENE ..." o "contiene ..." como declaracion de alergenos, devolve esos alergenos con "is_trace": false, porque forman parte declarada del producto.
- Si el ingrediente aparece dentro de una frase como "puede contener", "puede contener trazas de", "contiene trazas de" o "may contain", devolvelo con "is_trace": true.
- No incluyas la frase "puede contener" en el nombre; el nombre debe ser solo el ingrediente.
- Si el ingrediente esta en la lista principal de ingredientes, "is_trace" va en false.
- Cada nombre debe ser corto, idealmente 1 a 5 palabras.

Reglas para "ins":
- Si la etiqueta incluye un codigo INS junto al nombre, pone el numero en "ins" y el nombre en "nombre".
- Si la etiqueta solo muestra el codigo sin nombre, pone el codigo en "ins" y null en "nombre".
- Si el codigo es numerico con letra, conserva la letra, por ejemplo "160b".
- Si no hay codigo INS, "ins" va en null.

Si la foto no muestra una lista de ingredientes, devolve:
{ "ingredientes": [] }
PROMPT;
    }

    protected function ollamaPrompt(): string
    {
        return $this->systemPrompt()
            . "\n\nAnaliza la imagen adjunta. Responde usando JSON valido y nada mas.";
    }

    protected function client(): ClientContract
    {
        if ($this->provider() === 'azure') {
            $baseUri = preg_replace('#^https?://#', '', rtrim(config('openai.azure.endpoint'), '/'));

            return OpenAI::factory()
                ->withBaseUri($baseUri)
                ->withHttpHeader('api-key', config('openai.azure.api_key'))
                ->make();
        }

        return OpenAIFacade::getFacadeRoot();
    }

    protected function provider(): string
    {
        $configured = strtolower(trim((string) config('openai.ocr_provider', '')));

        if (in_array($configured, ['ollama_service', 'ollama', 'azure', 'openai'], true)) {
            return $configured;
        }

        return $this->isAzureConfigured() ? 'azure' : 'openai';
    }

    protected function isAzureConfigured(): bool
    {
        return ! empty(config('openai.azure.endpoint'))
            && ! empty(config('openai.azure.deployment'))
            && ! empty(config('openai.azure.api_key'));
    }

    protected function ensureProviderIsConfigured(): void
    {
        if ($this->provider() === 'ollama_service') {
            $missing = collect([
                'OCR_SERVICE_URL' => config('openai.ocr_service.url'),
                'OLLAMA_OCR_MODEL' => config('openai.ollama.model'),
            ])
                ->filter(fn($value) => trim((string) $value) === '')
                ->keys()
                ->implode(', ');

            if ($missing !== '') {
                throw new OcrConfigurationException("OCR mal configurado: faltan {$missing}.");
            }

            return;
        }

        if ($this->provider() === 'ollama') {
            $missing = collect([
                'OLLAMA_BASE_URL' => config('openai.ollama.base_url'),
                'OLLAMA_OCR_MODEL' => config('openai.ollama.model'),
            ])
                ->filter(fn($value) => trim((string) $value) === '')
                ->keys()
                ->implode(', ');

            if ($missing !== '') {
                throw new OcrConfigurationException("OCR mal configurado: faltan {$missing}.");
            }

            return;
        }

        $azureConfig = [
            'AZURE_OPENAI_ENDPOINT' => config('openai.azure.endpoint'),
            'AZURE_OPENAI_DEPLOYMENT' => config('openai.azure.deployment'),
            'AZURE_OPENAI_API_KEY' => config('openai.azure.api_key'),
        ];

        $hasAnyAzureConfig = collect($azureConfig)->contains(
            fn($value) => trim((string) $value) !== ''
        );

        if ($this->provider() === 'azure' || $hasAnyAzureConfig) {
            $missing = collect($azureConfig)
                ->filter(fn($value) => trim((string) $value) === '')
                ->keys()
                ->implode(', ');

            if ($missing !== '') {
                throw new OcrConfigurationException(
                    "OCR mal configurado: faltan {$missing}. Para Azure necesitas endpoint, deployment y api key."
                );
            }

            return;
        }

        if (trim((string) config('openai.api_key')) === '') {
            throw new OcrConfigurationException(
                'OCR mal configurado: configura OCR_PROVIDER=ollama_service, OCR_PROVIDER=ollama, OPENAI_API_KEY o Azure completo.'
            );
        }
    }
}

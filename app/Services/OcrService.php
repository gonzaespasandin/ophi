<?php

namespace App\Services;

use App\Models\Catalog\InsCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use OpenAI;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Facades\OpenAI as OpenAIFacade;

class OcrService
{
    /**
     * Extrae y normaliza la lista de ingredientes a partir de una foto de packaging.
     *
     * Flujo:
     *  1. GPT Vision analiza la imagen y devuelve un JSON estructurado con nombre
     *     e ins separados por ingrediente.
     *  2. Post-procesamiento: los ingredientes sin nombre se resuelven contra la
     *     tabla ins_codes del catálogo. Si el código no está en la tabla, se guarda
     *     como "ins NNN" para poder revisarlo después.
     *  3. Devuelve array de strings limpios listos para guardar en IMAGENES.ingredientes.
     *
     * @return string[]
     */
    public function extractIngredientsFromImage(UploadedFile $image): array
    {
        $base64   = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        $payload = [
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system',  'content' => $this->systemPrompt()],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type'      => 'image_url',
                            'image_url' => [
                                'url'    => "data:{$mimeType};base64,{$base64}",
                                'detail' => 'high',
                            ],
                        ],
                        ['type' => 'text', 'text' => 'Extraé los ingredientes de esta foto de packaging.'],
                    ],
                ],
            ],
        ];

        $payload['model'] = $this->isAzure()
            ? config('openai.azure.deployment')
            : config('openai.ocr_model', 'gpt-4o-mini');

        $response = $this->client()->chat()->create($payload);
        $content  = $response->choices[0]->message->content ?? '{}';
        $decoded  = json_decode($content, true);

        $rawItems = $decoded['ingredientes'] ?? [];

        return $this->resolveNames($rawItems);
    }

    // ─── Post-procesamiento ───────────────────────────────────────────────────

    /**
     * Recibe el array de objetos {nombre, ins} de GPT y devuelve strings normalizados.
     *
     * - Si tiene nombre → usar el nombre.
     * - Si no tiene nombre pero tiene ins → buscar en ins_codes; si no está, "ins [código]".
     * - Carga la tabla ins_codes una sola vez (eager) para no hacer N queries.
     */
    protected function resolveNames(array $items): array
    {
        if (empty($items)) {
            return [];
        }

        // Precarga solo los códigos que aparecen en esta respuesta
        $codesNeeded = collect($items)
            ->filter(fn($i) => empty($i['nombre'] ?? '') && ! empty($i['ins'] ?? ''))
            ->pluck('ins')
            ->map(fn($c) => strtolower(trim($c)))
            ->unique()
            ->values()
            ->all();

        $lookup = $codesNeeded
            ? InsCode::whereIn('code', $codesNeeded)->pluck('nombre', 'code')
            : collect();

        $result = [];
        foreach ($items as $item) {
            $nombre = trim($item['nombre'] ?? '');
            $ins    = strtolower(trim($item['ins'] ?? ''));

            if ($nombre !== '' && $ins !== '') {
                // Tiene nombre e INS: conservar ambos para mostrar "nombre (INS código)"
                $result[] = "{$nombre} (INS {$ins})";
            } elseif ($nombre !== '') {
                $result[] = $nombre;
            } elseif ($ins !== '') {
                // Solo código: resolver contra la tabla; si no está, dejarlo como "ins NNN"
                $insName  = $lookup[$ins] ?? null;
                $result[] = $insName !== null ? "{$insName} (INS {$ins})" : "ins {$ins}";
            }
            // si ambos vacíos, lo ignoramos
        }

        return array_values(array_filter($result));
    }

    // ─── Prompt ───────────────────────────────────────────────────────────────

    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
Sos un asistente especializado en extraer listas de ingredientes de fotos de packaging de alimentos argentinos.

**Formato de respuesta** — devolvé ÚNICAMENTE este JSON:
{
  "ingredientes": [
    { "nombre": "leche entera", "ins": null },
    { "nombre": "sorbato de potasio", "ins": "202" },
    { "nombre": null, "ins": "160b" }
  ]
}

**Reglas para el campo "nombre":**
- Minúsculas. Sin mayúsculas salvo nombres propios imprescindibles.
- Sin porcentajes ni valores numéricos ("leche 32%" → "leche entera").
- Sin paréntesis ni su contenido secundario, solo el nombre principal.
- Sin marcas comerciales ni nombres de empresa.
- Sin frases como "puede contener trazas de", "elaborado en", "contiene".
- Cada nombre es corto: 1-5 palabras. No repetir "ingrediente" o "contiene".

**Reglas para el campo "ins":**
- Si la etiqueta incluye un código INS junto al nombre (ej: "sorbato de potasio (INS 202)" o "sorbato de potasio 202"), poner el número en "ins" y el nombre en "nombre".
- Si la etiqueta solo muestra el código sin nombre (ej: "colorante 160b", "INS 471"), poner el código en "ins" y null en "nombre". NO intentes adivinar el nombre — el sistema lo resolverá con su tabla de referencia.
- Si el código es numérico con letra (ej: "160b"), conservar la letra ("160b", no "160").
- Si no hay código INS, "ins" va en null.

**Si la foto no muestra una lista de ingredientes, devolvé:**
{ "ingredientes": [] }
PROMPT;
    }

    // ─── Cliente OpenAI / Azure ───────────────────────────────────────────────

    protected function client(): ClientContract
    {
        if ($this->isAzure()) {
            $baseUri = preg_replace('#^https?://#', '', rtrim(config('openai.azure.endpoint'), '/'));

            return OpenAI::factory()
                ->withBaseUri($baseUri)
                ->withHttpHeader('api-key', config('openai.azure.api_key'))
                ->make();
        }

        return OpenAIFacade::getFacadeRoot();
    }

    protected function isAzure(): bool
    {
        return ! empty(config('openai.azure.endpoint'))
            && ! empty(config('openai.azure.deployment'))
            && ! empty(config('openai.azure.api_key'));
    }
}

<?php

namespace App\Services;

use App\Models\BarcodeSuggestion;
use App\Models\BarcodeSuggestionConfirmation;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class BarcodeSuggestionService
{
    const CONFIRMATIONS_NEEDED = 10;
    const DAILY_SUGGESTIONS_LIMIT = 5;
    const PENDING_BARCODE_TTL_MINUTES = 10;

    public function savePendingBarcode(string $barcode, int $userId): void
    {
        Cache::put("pending_barcode_{$userId}", [
            'barcode' => $barcode,
            'timestamp' => now()->toISOString(),
        ], now()->addMinutes(self::PENDING_BARCODE_TTL_MINUTES));
    }

    public function getPendingBarcode(int $userId): ?string
    {
        $data = Cache::get("pending_barcode_{$userId}");
        return $data['barcode'] ?? null;
    }

    public function clearPendingBarcode(int $userId): void
    {
        Cache::forget("pending_barcode_{$userId}");
    }

    public function canSuggest(int $userId, ?string $barcode = null, ?int $productId = null): bool
    {
        $withinDailyLimit = BarcodeSuggestion::where('suggested_by_user_id', $userId)
            ->whereDate('created_at', today())
            ->count() < self::DAILY_SUGGESTIONS_LIMIT;

        if (!$withinDailyLimit) {
            return false;
        }

        if ($barcode && $productId) {
            $suggestion = BarcodeSuggestion::where('barcode', $barcode)
                ->where('product_id', $productId)
                ->first();

            if ($suggestion) {
                $alreadyConfirmed = BarcodeSuggestionConfirmation::where('barcode_suggestion_id', $suggestion->id)
                    ->where('user_id', $userId)
                    ->exists();

                if ($alreadyConfirmed) {
                    return false;
                }
            }
        }

        return true;
    }

    public function suggest(string $barcode, int $productId, int $userId): array
    {
        $product = Product::find($productId);

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado',
            ];
        }

        $status = $this->resolveStatus($barcode, $product);

        $suggestion = BarcodeSuggestion::firstOrCreate([
            'barcode' => $barcode,
            'product_id' => $productId,
        ], [
            'suggested_by_user_id' => $userId,
            'status' => $status,
        ]);

        $alreadyConfirmed = BarcodeSuggestionConfirmation::where([
            'barcode_suggestion_id' => $suggestion->id,
            'user_id' => $userId,
        ])->exists();

        if ($alreadyConfirmed) {
            return [
                'success' => false,
                'message' => 'Código ya confirmado anteriormente',
            ];
        }

        BarcodeSuggestionConfirmation::create([
            'barcode_suggestion_id' => $suggestion->id,
            'user_id' => $userId,
        ]);

        $this->tryAutoApprove($suggestion);

        return [
            'success' => true,
            'message' => 'Código sugerido correctamente',
        ];
    }

    private function resolveStatus(string $barcode, Product $product): string
    {
        $barcodeExistsElsewhere = Product::where('barcode', $barcode)
            ->where('id', '!=', $product->id)
            ->exists();

        if ($barcodeExistsElsewhere || !empty($product->barcode)) {
            return 'blocked';
        }

        return 'pending';
    }

    private function tryAutoApprove(BarcodeSuggestion $suggestion): void
    {
        if ($suggestion->status !== 'pending') {
            return;
        }

        $count = BarcodeSuggestionConfirmation::where('barcode_suggestion_id', $suggestion->id)->count();

        if ($count < self::CONFIRMATIONS_NEEDED) {
            return;
        }

        $product = Product::find($suggestion->product_id);
        
        if (!$product || !empty($product->barcode)) {
            $suggestion->update(['status' => 'blocked']);
            return;
        }

        $barcodeExistsElsewhere = Product::where('barcode', $suggestion->barcode)
            ->where('id', '!=', $product->id)
            ->exists();

        if ($barcodeExistsElsewhere) {
            $suggestion->update(['status' => 'blocked']);
            return;
        }

        $product->update(['barcode' => $suggestion->barcode]);
        $suggestion->update(['status' => 'approved']);
    }
}

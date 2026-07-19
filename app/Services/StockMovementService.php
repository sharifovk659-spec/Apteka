<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;

class StockMovementService
{
    public function recordSale(Product $product, int $quantity, string $referenceType, int $referenceId, ?string $note = null, ?int $userId = null): void
    {
        $before = (int) $product->stock;
        $after = max(0, $before - $quantity);

        StockMovement::query()->create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'user_id' => $userId,
        ]);
    }

    public function recordReturn(Product $product, int $quantity, string $referenceType, int $referenceId, ?string $note = null, ?int $userId = null): void
    {
        $before = (int) $product->stock;
        $after = $before + $quantity;

        StockMovement::query()->create([
            'product_id' => $product->id,
            'type' => 'return',
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'user_id' => $userId,
        ]);
    }

    public function hasReturnFor(string $referenceType, int $referenceId): bool
    {
        return StockMovement::query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('type', 'return')
            ->exists();
    }
}

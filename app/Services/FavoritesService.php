<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class FavoritesService
{
    private const SESSION_KEY = 'favorites';

    public function ids(): array
    {
        return array_values(array_unique(array_map('intval', Session::get(self::SESSION_KEY, []))));
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function toggle(int $productId): bool
    {
        $ids = $this->ids();

        if (in_array($productId, $ids, true)) {
            $ids = array_values(array_filter($ids, fn (int $id) => $id !== $productId));
            Session::put(self::SESSION_KEY, $ids);

            return false;
        }

        $ids[] = $productId;
        Session::put(self::SESSION_KEY, $ids);

        return true;
    }

    public function remove(int $productId): void
    {
        Session::put(
            self::SESSION_KEY,
            array_values(array_filter($this->ids(), fn (int $id) => $id !== $productId)),
        );
    }

    public function items(): Collection
    {
        $ids = $this->ids();

        if ($ids === []) {
            return collect();
        }

        $products = Product::query()
            ->select([
                'id', 'name', 'slug', 'price', 'old_price',
                'main_image', 'manufacturer', 'brand_id',
            ])
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->withListingRelations()
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $products->get($id))
            ->filter()
            ->values();
    }
}

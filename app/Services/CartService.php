<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    private ?Collection $itemsCache = null;

    public function items(): Collection
    {
        if ($this->itemsCache !== null) {
            return $this->itemsCache;
        }

        $cart = $this->raw();

        if ($cart === []) {
            return $this->itemsCache = collect();
        }

        $products = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'stock', 'main_image', 'is_active'])
            ->whereIn('id', array_keys($cart))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return $this->itemsCache = collect($cart)
            ->map(function (int $quantity, int $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product || $product->stock <= 0) {
                    return null;
                }

                $quantity = min($quantity, $product->stock);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => round($product->price * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    public function subtotal(?Collection $items = null): float
    {
        $items ??= $this->items();

        return (float) $items->sum('line_total');
    }

    public function isEmpty(): bool
    {
        return $this->raw() === [];
    }

    public function add(int $productId, int $quantity = 1): array
    {
        $product = $this->findAvailableProduct($productId);
        $cart = $this->raw();
        $current = $cart[$productId] ?? 0;
        $newQuantity = min($current + $quantity, $product->stock);

        if ($newQuantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $newQuantity;
        }

        $this->persist($cart);

        return $this->result('Товар добавлен в корзину.');
    }

    public function update(int $productId, int $quantity): array
    {
        $product = $this->findAvailableProduct($productId);
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$productId]);

            $this->persist($cart);

            return $this->result('Товар удалён из корзины.');
        }

        if ($quantity > $product->stock) {
            return $this->error("Доступно только {$product->stock} шт.");
        }

        $cart[$productId] = $quantity;
        $this->persist($cart);

        return $this->result('Количество обновлено.');
    }

    public function remove(int $productId): array
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        $this->persist($cart);

        return $this->result('Товар удалён из корзины.');
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        $this->invalidateCache();
    }

    public function syncWithStock(): void
    {
        $cart = $this->raw();

        if ($cart === []) {
            return;
        }

        $products = Product::query()
            ->select(['id', 'stock', 'is_active'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product || ! $product->is_active || $product->stock <= 0) {
                unset($cart[$productId]);
                continue;
            }

            $cart[$productId] = min($quantity, $product->stock);
        }

        $this->persist($cart);
    }

    private function findAvailableProduct(int $productId): Product
    {
        $product = Product::query()
            ->select(['id', 'name', 'stock', 'is_active'])
            ->where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            throw new \InvalidArgumentException('Товар недоступен.');
        }

        if ($product->stock <= 0) {
            throw new \InvalidArgumentException('Товар отсутствует на складе.');
        }

        return $product;
    }

    private function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    private function persist(array $cart): void
    {
        if ($cart === []) {
            Session::forget(self::SESSION_KEY);
        } else {
            Session::put(self::SESSION_KEY, $cart);
        }

        $this->invalidateCache();
    }

    private function invalidateCache(): void
    {
        $this->itemsCache = null;
    }

    private function result(string $message): array
    {
        return ['success' => true, 'message' => $message];
    }

    private function error(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}

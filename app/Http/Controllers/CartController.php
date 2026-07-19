<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function index(): View
    {
        $this->cart->syncWithStock();

        $items = $this->cart->items();

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal($items),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $result = $this->cart->add(
                (int) $validated['product_id'],
                (int) ($validated['quantity'] ?? 1),
            );
        } catch (\InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message'],
        );
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        try {
            $result = $this->cart->update($product, (int) $validated['quantity']);
        } catch (\InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message'],
        );
    }

    public function remove(int $product): RedirectResponse
    {
        $result = $this->cart->remove($product);

        return back()->with('success', $result['message']);
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back()->with('success', 'Корзина очищена.');
    }
}

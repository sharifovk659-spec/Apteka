<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\FavoritesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoritesController extends Controller
{
    public function __construct(
        private readonly FavoritesService $favorites,
    ) {}

    public function index(): View
    {
        return view('favorites.index', [
            'products' => $this->favorites->items(),
        ]);
    }

    public function toggle(Product $product): RedirectResponse
    {
        if (! $product->is_active) {
            return back()->with('error', 'Товар недоступен.');
        }

        $added = $this->favorites->toggle($product->id);

        return back()->with(
            'success',
            $added ? 'Товар добавлен в избранное.' : 'Товар удалён из избранного.',
        );
    }

    public function remove(Product $product): RedirectResponse
    {
        $this->favorites->remove($product->id);

        return back()->with('success', 'Товар удалён из избранного.');
    }
}

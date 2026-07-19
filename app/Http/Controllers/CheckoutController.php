<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SettingService;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderService $orders,
        private readonly SettingService $settings,
    ) {}

    public function index(): View|RedirectResponse
    {
        $this->cart->syncWithStock();

        if ($this->cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Добавьте товары в корзину перед оформлением заказа.');
        }

        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal($items);

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'deliveryPrice' => $this->settings->deliveryPrice(),
            'minOrderAmount' => $this->settings->minOrderAmount(),
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->orders->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('checkout.success', $order->order_number)
            ->with('success', 'Заказ успешно оформлен.');
    }

    public function success(string $orderNumber): View
    {
        $order = Order::query()
            ->select([
                'id', 'order_number', 'customer_name', 'customer_phone',
                'total', 'delivery_type', 'payment_method', 'status', 'created_at',
            ])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}

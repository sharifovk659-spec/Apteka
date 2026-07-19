@props(['product', 'lazy' => true, 'variant' => 'grid', 'isFavorite' => null])

@php
    $manufacturer = $product->brand?->name ?? $product->manufacturer;
    $isFavorite = $isFavorite ?? in_array($product->id, $favoriteProductIds ?? [], true);
@endphp

<article @class(['product-card', 'product-card--list' => $variant === 'list'])>
    <div class="product-card__image-wrap">
        <a href="{{ route('product.show', $product->slug) }}" class="product-card__image-link">
            @if($product->discountPercent())
                <span class="product-card__badge">−{{ $product->discountPercent() }}%</span>
            @endif
            <x-product-image :product="$product" :lazy="$lazy" />
        </a>
        <form action="{{ route('favorites.toggle', $product) }}" method="POST" class="product-card__favorite-form">
            @csrf
            <button
                type="submit"
                @class(['product-card__favorite', 'is-active' => $isFavorite])
                aria-label="{{ $isFavorite ? 'Удалить из избранного' : 'Добавить в избранное' }}"
                aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
            >
                <x-icon name="heart" class="product-card__favorite-icon" />
            </button>
        </form>
    </div>
    <div class="product-card__body">
        <h3 class="product-card__title">
            <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        @if($manufacturer)
            <p class="product-card__brand">{{ $manufacturer }}</p>
        @endif
        <div class="product-card__footer">
            <div class="product-card__prices">
                <span class="product-card__price">{{ $product->formattedPrice() }}</span>
                @if($product->formattedOldPrice())
                    <span class="product-card__old-price">{{ $product->formattedOldPrice() }}</span>
                @endif
            </div>
            <form action="{{ route('cart.add') }}" method="POST" class="product-card__cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="product-card__cart-btn" aria-label="Добавить {{ $product->name }} в корзину">
                    <x-icon name="plus-cart" />
                </button>
            </form>
        </div>
    </div>
</article>

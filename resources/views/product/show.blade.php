@extends('layouts.app')

@section('title', $product->name)

@section('content')
    @php
        $gallery = $product->galleryImages();
        $primaryImage = $gallery->firstWhere('is_primary', true) ?? $gallery->first();
        $categoryTrail = collect();
        $trailCategory = $product->category;

        while ($trailCategory) {
            $categoryTrail->prepend([
                'label' => $trailCategory->name,
                'url' => route('catalog.index', ['category' => $trailCategory->slug]),
            ]);
            $trailCategory = $trailCategory->parent;
        }

        $breadcrumbs = collect([
            ['label' => 'Каталог', 'url' => route('catalog.index')],
        ])->merge($categoryTrail);

        $isFavorite = in_array($product->id, $favoriteProductIds ?? [], true);
    @endphp

    <div class="container product-page">
        <nav class="product-page__breadcrumbs" aria-label="Хлебные крошки">
            @foreach($breadcrumbs as $crumb)
                @if($loop->last)
                    <span>{{ $crumb['label'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    <span class="product-page__breadcrumb-sep">/</span>
                @endif
            @endforeach
        </nav>

        <section class="product-page__hero">
            <div class="product-page__gallery" id="product-gallery">
                <div class="product-page__main-image">
                    @if($primaryImage)
                        <img
                            id="product-main-image"
                            src="{{ $primaryImage->imageUrl() }}"
                            alt="{{ $primaryImage->alt_text ?? $product->name }}"
                            width="520"
                            height="520"
                        >
                    @else
                        <img
                            id="product-main-image"
                            src="{{ $product->mainImageUrl() }}"
                            alt="{{ $product->name }}"
                            width="520"
                            height="520"
                        >
                    @endif
                </div>

                @if($gallery->count() > 1)
                    <div class="product-page__thumbs" role="list">
                        @foreach($gallery as $image)
                            <button
                                type="button"
                                class="product-page__thumb @if($image->is_primary || ($loop->first && ! $gallery->contains(fn ($item) => $item->is_primary))) is-active @endif"
                                data-image-url="{{ $image->imageUrl() }}"
                                data-image-alt="{{ $image->alt_text ?? $product->name }}"
                                aria-label="Показать изображение {{ $loop->iteration }}"
                            >
                                <img
                                    src="{{ $image->imageUrl() }}"
                                    alt=""
                                    width="72"
                                    height="72"
                                    loading="lazy"
                                >
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="product-page__info">
                <h1 class="product-page__title">{{ $product->name }}</h1>

                @if($product->brand || $product->manufacturer)
                    <p class="product-page__brand">{{ $product->brand?->name ?? $product->manufacturer }}</p>
                @endif

                <div class="product-page__meta">
                    <span>SKU: {{ $product->sku }}</span>
                    @if($product->requires_prescription)
                        <span class="product-page__badge">По рецепту</span>
                    @endif
                    @if($product->stock > 0)
                        <span class="product-page__stock product-page__stock--in">В наличии</span>
                    @else
                        <span class="product-page__stock product-page__stock--out">Нет в наличии</span>
                    @endif
                </div>

                <div class="product-page__prices">
                    <span class="product-page__price">{{ $product->formattedPrice() }}</span>
                    @if($product->formattedOldPrice())
                        <span class="product-page__old-price">{{ $product->formattedOldPrice() }}</span>
                        @if($product->discountPercent())
                            <span class="product-page__discount">−{{ $product->discountPercent() }}%</span>
                        @endif
                    @endif
                </div>

                @if($product->short_description)
                    <p class="product-page__short">{{ $product->short_description }}</p>
                @endif

                <div class="product-page__actions">
                    <form action="{{ route('cart.add') }}" method="POST" class="product-page__cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn--primary btn--lg" @disabled($product->stock <= 0)>
                            Добавить в корзину
                        </button>
                    </form>
                    <form action="{{ route('favorites.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn--outline btn--lg @if($isFavorite) is-active @endif">
                            {{ $isFavorite ? 'В избранном' : 'В избранное' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="product-page__details">
            @if($product->description)
                <div class="product-page__block">
                    <h2>Описание</h2>
                    <div class="product-page__text">{!! nl2br(e($product->description)) !!}</div>
                </div>
            @endif
            @if($product->composition)
                <div class="product-page__block">
                    <h2>Состав</h2>
                    <div class="product-page__text">{!! nl2br(e($product->composition)) !!}</div>
                </div>
            @endif
            @if($product->usage_instructions)
                <div class="product-page__block">
                    <h2>Инструкция по применению</h2>
                    <div class="product-page__text">{!! nl2br(e($product->usage_instructions)) !!}</div>
                </div>
            @endif
            @if($product->contraindications)
                <div class="product-page__block">
                    <h2>Противопоказания</h2>
                    <div class="product-page__text">{!! nl2br(e($product->contraindications)) !!}</div>
                </div>
            @endif
            <div class="product-page__specs">
                @if($product->dosage_form)<p><strong>Форма:</strong> {{ $product->dosage_form }}</p>@endif
                @if($product->dosage)<p><strong>Дозировка:</strong> {{ $product->dosage }}</p>@endif
                @if($product->country)<p><strong>Страна:</strong> {{ $product->country }}</p>@endif
                @if($product->barcode)<p><strong>Штрихкод:</strong> {{ $product->barcode }}</p>@endif
            </div>
        </section>

        @if($relatedProducts->isNotEmpty())
            <section class="product-page__related">
                <h2 class="section-title">Похожие товары</h2>
                <div class="products-grid">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @vite('resources/js/product-page.js')
@endpush

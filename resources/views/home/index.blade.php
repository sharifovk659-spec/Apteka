@extends('layouts.app')

@section('title', 'Главная')
@section('main-class', 'main--home')

@section('content')
    {{-- Banner grid: left image | center slider | right image --}}
    @if($leftBanner || $sliderBanners->isNotEmpty() || $rightBanner)
        <section class="banner-grid-section">
            <div class="container">
                <div class="banner-grid">
                    <div class="banner-grid__side banner-grid__side--left">
                        @if($leftBanner)
                            <x-banner-card :banner="$leftBanner" />
                        @endif
                    </div>

                    <div class="banner-grid__center">
                        @if($sliderBanners->isNotEmpty())
                            <div class="banner-slider" id="home-banner-slider">
                                <div class="banner-slider__track">
                                    @foreach($sliderBanners as $index => $banner)
                                        <div class="banner-slider__slide {{ $index === 0 ? 'is-active' : '' }}" data-slide="{{ $index }}">
                                            <x-banner-card :banner="$banner" variant="slider" :lazy="$index > 0" />
                                        </div>
                                    @endforeach
                                </div>

                                @if($sliderBanners->count() > 1)
                                    <button type="button" class="banner-slider__arrow banner-slider__arrow--prev" aria-label="Предыдущий слайд">
                                        <x-icon name="chevron-left" />
                                    </button>
                                    <button type="button" class="banner-slider__arrow banner-slider__arrow--next" aria-label="Следующий слайд">
                                        <x-icon name="chevron-right" />
                                    </button>
                                    <div class="banner-slider__dots" role="tablist" aria-label="Слайды баннера">
                                        @foreach($sliderBanners as $index => $banner)
                                            <button
                                                type="button"
                                                class="banner-slider__dot {{ $index === 0 ? 'is-active' : '' }}"
                                                data-slide-to="{{ $index }}"
                                                aria-label="Слайд {{ $index + 1 }}"
                                                @if($index === 0) aria-current="true" @endif
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="banner-grid__side banner-grid__side--right">
                        @if($rightBanner)
                            <x-banner-card :banner="$rightBanner" :lazy="true" />
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($homeCategories->isNotEmpty())
        <section class="section section--surface">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Категории</h2>
                    <a href="{{ route('catalog.index') }}" class="section__link">Весь каталог</a>
                </div>
                <div class="category-grid">
                    @foreach($homeCategories as $category)
                        <x-category-tile :category="$category" />
                    @endforeach
                </div>
                @if($homeSubcategories->isNotEmpty())
                    <div class="home-subcategories">
                        @foreach($homeSubcategories->take(12) as $subcategory)
                            <a
                                href="{{ route('catalog.index', ['category' => $subcategory->slug]) }}"
                                class="header-chip"
                                style="margin: 4px 8px 4px 0"
                            >
                                {{ str_repeat('—', max(0, ($subcategory->tree_depth ?? 1) - 1)) }}{{ ($subcategory->tree_depth ?? 1) > 1 ? ' ' : '' }}{{ $subcategory->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if($dailyProducts->isNotEmpty())
        <section class="section section--surface">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Товары дня</h2>
                    <a href="{{ route('catalog.index') }}" class="section__link">Смотреть все</a>
                </div>
                <div class="product-scroll">
                    <div class="product-grid product-grid--scroll">
                        @foreach($dailyProducts as $product)
                            <x-product-card :product="$product" :lazy="true" />
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($promoProducts->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Акции</h2>
                    <a href="{{ route('catalog.index', ['discount' => 1]) }}" class="section__link">Все акции</a>
                </div>

                <div class="promo-slider" id="home-promo-slider">
                    <button type="button" class="promo-slider__arrow promo-slider__arrow--prev" aria-label="Предыдущая акция">
                        <x-icon name="chevron-left" />
                    </button>

                    <div class="promo-slider__track">
                        @foreach($promoProducts as $index => $product)
                            <a href="{{ route('product.show', $product->slug) }}" class="promo-slider__item">
                                <img
                                    src="{{ $product->mainImageUrl() }}"
                                    alt="{{ $product->name }}"
                                    class="promo-slider__image"
                                    width="360"
                                    height="220"
                                    @if($index > 1) loading="lazy" @endif
                                >
                                <span class="promo-slider__caption">
                                    @if($product->discountPercent())
                                        <span class="promo-slider__badge">−{{ $product->discountPercent() }}%</span>
                                    @endif
                                    <span class="promo-slider__title">{{ $product->name }}</span>
                                    <span class="promo-slider__price">
                                        <strong>{{ $product->formattedPrice() }}</strong>
                                        @if($product->formattedOldPrice())
                                            <s>{{ $product->formattedOldPrice() }}</s>
                                        @endif
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <button type="button" class="promo-slider__arrow promo-slider__arrow--next" aria-label="Следующая акция">
                        <x-icon name="chevron-right" />
                    </button>
                </div>
            </div>
        </section>
    @endif

    @if($bestsellers->isNotEmpty())
        <section class="section section--surface">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Хиты продаж</h2>
                    <a href="{{ route('catalog.index') }}" class="section__link">Смотреть все</a>
                </div>
                <div class="product-grid">
                    @foreach($bestsellers as $product)
                        <x-product-card :product="$product" :lazy="true" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="benefits-wide">
        <div class="container">
            <div class="benefits-wide__grid">
                <div class="benefits-wide__item">
                    <x-icon name="star" class="benefits-wide__icon" />
                    <h3 class="benefits-wide__title">Широкий ассортимент</h3>
                    <p class="benefits-wide__text">Лекарства, витамины, гигиена и медтехника в одном месте</p>
                </div>
                <div class="benefits-wide__item">
                    <x-icon name="truck" class="benefits-wide__icon" />
                    <h3 class="benefits-wide__title">Доставка на дом</h3>
                    <p class="benefits-wide__text">Быстрая доставка по Душанбе и области</p>
                </div>
                <div class="benefits-wide__item">
                    <x-icon name="shield" class="benefits-wide__icon" />
                    <h3 class="benefits-wide__title">Выгодные цены</h3>
                    <p class="benefits-wide__text">Регулярные акции и специальные предложения</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="cta-search">
                <div class="cta-search__content">
                    <h2 class="cta-search__title">Не нашли нужный товар?</h2>
                    <p class="cta-search__text">Введите название лекарства или действующее вещество</p>
                </div>
                <form action="{{ route('catalog.index') }}" method="GET" class="cta-search__form">
                    <input type="search" name="q" class="cta-search__input" placeholder="Например: парацетамол" aria-label="Поиск лекарства">
                    <button type="submit" class="btn btn--primary">Найти лекарство</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @vite('resources/js/home-page.js')
@endpush

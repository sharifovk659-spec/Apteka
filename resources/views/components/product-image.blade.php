@props(['product', 'width' => 220, 'height' => 220, 'lazy' => true, 'class' => 'product-card__image'])

<img
    src="{{ $product->mainImageUrl() }}"
    alt="{{ $product->name }}"
    class="{{ $class }}"
    width="{{ $width }}"
    height="{{ $height }}"
    @if($lazy) loading="lazy" @endif
>

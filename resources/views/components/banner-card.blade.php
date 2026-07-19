@props(['banner', 'variant' => 'side', 'lazy' => false])

@php
    $sizes = match ($variant) {
        'slider' => ['width' => 920, 'height' => 320, 'class' => 'banner-card__image--slider'],
        default => ['width' => 320, 'height' => 420, 'class' => 'banner-card__image--side'],
    };
    $imageUrl = $banner->imageUrl();
    $linkUrl = $banner->linkUrl();
@endphp

<a href="{{ $linkUrl }}" @class(['banner-card', 'banner-card--'.$variant])>
    <img
        src="{{ $imageUrl }}"
        alt="{{ $banner->title }}"
        class="banner-card__image {{ $sizes['class'] }}"
        width="{{ $sizes['width'] }}"
        height="{{ $sizes['height'] }}"
        @if($lazy) loading="lazy" @endif
    >
    <span class="banner-card__overlay">
        <span class="banner-card__title">{{ $banner->title }}</span>
        @if($banner->subtitle)
            <span class="banner-card__subtitle">{{ $banner->subtitle }}</span>
        @endif
    </span>
</a>

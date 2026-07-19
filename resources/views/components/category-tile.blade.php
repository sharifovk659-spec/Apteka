@props(['category'])

<a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="category-card-new">
    <span class="category-card-new__media">
        <img
            src="{{ $category->coverImageUrl() }}"
            alt="{{ $category->name }}"
            class="category-card-new__image"
            width="160"
            height="120"
            loading="lazy"
        >
        <span @class(['category-card-new__icon', 'category-card-new__icon--'.$category->icon])>
            <x-icon :name="$category->iconName()" />
        </span>
    </span>
    <span class="category-card-new__body">
        <span class="category-card-new__name">{{ $category->name }}</span>
        <span class="category-card-new__count">{{ $category->products_count }} товаров</span>
    </span>
</a>

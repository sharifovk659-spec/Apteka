@if($categories->isNotEmpty())
    @foreach($categories as $child)
        @if(! empty($mobile))
            <a href="{{ route('catalog.index', ['category' => $child->slug]) }}" style="padding-left: {{ 16 + ($depth * 14) }}px">
                {{ str_repeat('—', $depth) }}{{ $depth > 0 ? ' ' : '' }}{{ $child->name }}
            </a>
        @else
            <a
                href="{{ route('catalog.index', ['category' => $child->slug]) }}"
                class="header-catalog__item header-catalog__item--child"
                style="padding-left: {{ 16 + ($depth * 14) }}px"
            >
                {{ str_repeat('—', $depth) }}{{ $depth > 0 ? ' ' : '' }}{{ $child->name }}
            </a>
        @endif
        @include('partials.public.category-nav-children', [
            'categories' => $child->children,
            'depth' => $depth + 1,
            'mobile' => $mobile ?? false,
        ])
    @endforeach
@endif

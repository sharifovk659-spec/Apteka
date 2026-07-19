@props([
    'title' => 'Нет данных',
    'text' => 'В этом разделе пока ничего нет.',
    'actionLabel' => null,
    'actionUrl' => null,
])

<div {{ $attributes->class(['admin-empty-state']) }}>
    <div class="admin-empty-state__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="4" y="4" width="16" height="16" rx="3"/>
            <path d="M8 12h8"/>
        </svg>
    </div>
    <h2 class="admin-empty-state__title">{{ $title }}</h2>
    <p class="admin-empty-state__text">{{ $text }}</p>
    @if($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="admin-btn admin-btn--primary">{{ $actionLabel }}</a>
    @endif
</div>

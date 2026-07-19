@props([
    'label',
    'value',
    'hint' => null,
    'trend' => null,
    'icon' => null,
])

<div class="admin-stat-card">
    <div class="admin-stat-card__head">
        <p class="admin-stat-card__label">{{ $label }}</p>
        @if($icon)
            <span class="admin-stat-card__icon" aria-hidden="true">
                <x-admin-icon :name="$icon" />
            </span>
        @endif
    </div>
    <p class="admin-stat-card__value">{{ $value }}</p>
    @if($hint)
        <p class="admin-stat-card__hint">{{ $hint }}</p>
    @endif
    @if($trend)
        <p @class(['admin-stat-card__trend', 'is-positive' => str_starts_with($trend, '+'), 'is-negative' => str_starts_with($trend, '-')])>{{ $trend }}</p>
    @endif
</div>

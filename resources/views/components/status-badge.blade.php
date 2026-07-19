@props([
    'status',
    'label' => null,
])

@php
    use App\Support\OrderStatus;

    $labels = array_merge(OrderStatus::labels(), [
        'active' => 'Активен',
        'inactive' => 'Неактивен',
    ]);

    $variants = [
        'success' => 'status-badge--success',
        'warning' => 'status-badge--warning',
        'danger' => 'status-badge--danger',
        'neutral' => 'status-badge--neutral',
        OrderStatus::NEW => 'status-badge--new',
        OrderStatus::CONFIRMED => 'status-badge--confirmed',
        OrderStatus::PROCESSING => 'status-badge--processing',
        OrderStatus::DELIVERING => 'status-badge--delivering',
        OrderStatus::COMPLETED => 'status-badge--completed',
        OrderStatus::CANCELLED => 'status-badge--cancelled',
        'active' => 'status-badge--completed',
        'inactive' => 'status-badge--neutral',
        'pending' => 'status-badge--new',
        'shipped' => 'status-badge--delivering',
        'delivered' => 'status-badge--completed',
    ];

    $class = $variants[$status] ?? 'status-badge--neutral';
    $text = $label ?? ($labels[$status] ?? ucfirst($status));
@endphp

<span {{ $attributes->class(['status-badge', $class]) }}>{{ $text }}</span>

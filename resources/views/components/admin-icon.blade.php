@props(['name', 'class' => ''])

@switch($name)
    @case('dashboard')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z"/></svg>
        @break
    @case('products')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        @break
    @case('categories')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/></svg>
        @break
    @case('orders')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M9 5H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9h-5"/><path d="M9 5V3h6v2"/></svg>
        @break
    @case('customers')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c1.5-3 4-4.5 6-4.5s4.5 1.5 6 4.5"/><path d="M14 20c.5-2 2-3 3-3s2.5 1 3 3"/></svg>
        @break
    @case('warehouse')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 9l9-5 9 5v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21V12h6v9"/></svg>
        @break
    @case('promotions')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 2.4 5.8 6.3.5-4.8 4.1 1.5 6.1L12 17.8 6.6 19.5l1.5-6.1L3.3 9.3l6.3-.5z"/></svg>
        @break
    @case('reviews')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3 1.5-5.5A4 4 0 0 1 4 15V7a4 4 0 0 1 4-4h9a4 4 0 0 1 4 4z"/></svg>
        @break
    @case('reports')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15V9M12 15V7M16 15v-5"/></svg>
        @break
    @case('settings')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        @break
    @case('users')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.5-4 6.5-6 8-6s6.5 2 8 6"/></svg>
        @break
    @case('logout')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l-1-1 4-4-4-4 1-1"/><path d="M14 12H4M4 4h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4"/></svg>
        @break
    @case('bell')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 17H9l-6 2V11a8 8 0 1 1 16 0v8z"/></svg>
        @break
    @case('chevron-left')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m14 6-6 6 6 6"/></svg>
        @break
    @case('chevron-right')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m10 6 6 6-6 6"/></svg>
        @break
    @case('menu')
        <svg @class(['admin-icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        @break
@endswitch

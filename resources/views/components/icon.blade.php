@props(['name', 'class' => ''])

@switch($name)
    @case('cart')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>
        @break
    @case('orders')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M9 5H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9h-5"/><path d="M9 5V3h6v2"/><path d="M9 12h6M9 16h4"/></svg>
        @break
    @case('heart')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20s-7-4.5-9-8.5C1.5 8.5 3.5 5 7 5c2 0 3.5 1.5 5 3 1.5-1.5 3-3 5-3 3.5 0 5.5 3.5 4 6.5-2 4-9 8.5-9 8.5z"/></svg>
        @break
    @case('user')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.5-4 6.5-6 8-6s6.5 2 8 6"/></svg>
        @break
    @case('search')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        @break
    @case('burger')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        @break
    @case('close')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
        @break
    @case('home')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z"/></svg>
        @break
    @case('catalog')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/></svg>
        @break
    @case('chevron-down')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        @break
    @case('chevron-left')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
        @break
    @case('chevron-right')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
        @break
    @case('location')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s7-4.5 7-10a7 7 0 1 0-14 0c0 5.5 7 10 7 10z"/><circle cx="12" cy="11" r="2.5"/></svg>
        @break
    @case('pill')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 9 6 6M8.5 15.5 15.5 8.5a3.5 3.5 0 1 0-5 5z"/></svg>
        @break
    @case('vitamin')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="7" y="3" width="10" height="18" rx="5"/><path d="M12 8v8"/></svg>
        @break
    @case('baby')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3"/><path d="M7 20c.5-3 2.5-5 5-5s4.5 2 5 5"/></svg>
        @break
    @case('hygiene')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 4v4M16 4v4"/><rect x="6" y="8" width="12" height="12" rx="3"/></svg>
        @break
    @case('beauty')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3c3 4 3 8 0 12-3-4-3-8 0-12z"/><path d="M8 21h8"/></svg>
        @break
    @case('medical')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="8" width="14" height="12" rx="2"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/><path d="M12 12v4M10 14h4"/></svg>
        @break
    @case('star')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 2.4 5.8 6.3.5-4.8 4.1 1.5 6.1L12 17.8 6.6 19.5l1.5-6.1L3.3 9.3l6.3-.5z"/></svg>
        @break
    @case('truck')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="18" r="1.5"/><circle cx="18" cy="18" r="1.5"/></svg>
        @break
    @case('shield')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3 19 6v6c0 4.5-3.5 7.5-7 9-3.5-1.5-7-4.5-7-9V6z"/><path d="m9 12 2 2 4-4"/></svg>
        @break
    @case('plus-cart')
        <svg @class(['icon', $class]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M12 8v6M9 11h6"/></svg>
        @break
@endswitch

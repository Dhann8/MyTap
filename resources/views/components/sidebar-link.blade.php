@props(['href' => '#', 'icon' => null, 'active' => false])

@php
    $classes = $active
        ? 'flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50/80 rounded-xl transition-all duration-200'
        : 'flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all duration-200';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="flex items-center justify-center w-5 h-5 text-base shrink-0 opacity-80">
            {!! $icon !!}
        </span>
    @endif

    <div class="flex flex-row gap-3 items-center justify-center">{{ $slot }}</div>
</a>
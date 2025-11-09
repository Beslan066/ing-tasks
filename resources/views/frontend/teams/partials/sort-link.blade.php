@php
    $currentSort = $currentSort ?? 'id';
    $currentDirection = $currentDirection ?? 'desc';
    $isCurrent = $currentSort === $field;
    $newDirection = $isCurrent && $currentDirection === 'asc' ? 'desc' : 'asc';
    $icon = $isCurrent ? ($currentDirection === 'asc' ? '↑' : '↓') : '';
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'direction' => $newDirection]) }}"
   class="flex items-center space-x-1 hover:text-blue-600 {{ $isCurrent ? 'text-blue-600 font-semibold' : '' }}">
    <span>{{ $label }}</span>
    @if($icon)
        <span class="text-xs">{{ $icon }}</span>
    @endif
</a>

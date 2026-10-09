{{-- resources/views/components/form/input.blade.php --}}
@props([
    'name',
    'label'       => null,
    'type'        => 'text',  // text, number, email, password, tel, url, date и т.д.
    'required'    => false,
    'id'          => null,
    'placeholder' => null,
    'value'       => null,
])

@php
    $id = $id ?? $name;
    $current = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div id="{{ $id }}-field" {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label for="{{ $id }}" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            placeholder="{{ $placeholder }}"
            value="{{ $current }}"
            {{ $required ? 'required' : '' }}
            class="w-full px-4 py-2.5 bg-slate-50/50 border {{ $hasError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/10' }} rounded-xl focus:bg-white focus:outline-none focus:ring-4 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
        >
    </div>

    @error($name)
        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- resources/views/components/form/textarea.blade.php --}}
@props([
    'name',
    'label'       => null,
    'required'    => false,
    'id'          => null,
    'placeholder' => null,
    'value'       => null,
    'rows'        => 3,
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
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full px-4 py-3 bg-slate-50/50 border {{ $hasError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/10' }} rounded-xl focus:bg-white focus:outline-none focus:ring-4 transition-all duration-200 resize-none text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
        >{{ $current }}</textarea>
    </div>

    @error($name)
        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

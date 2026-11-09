{{-- resources/views/components/form/inputselect.blade.php --}}
@props([
    'name'        => null,
    'label'       => null,
    'options'     => [],
    'selected'    => null,
    'required'    => false,
    'id'          => null,
    'placeholder' => null,
    'emptyText'   => 'Список пуст',
    'variant'     => 'light', // light | dark | glass
    'ariaLabel'   => null,
])

@php
    $id   = $id ?? $name;
    $name = $name ?? $id;

    $options = $options instanceof \Illuminate\Contracts\Support\Arrayable ? $options->toArray() : $options;

    $current = old($name, $selected);
    if ($current instanceof \BackedEnum) {
        $current = $current->value;
    }
    if ($current === null || $current === '') {
        $current = $placeholder ? '' : (string) array_key_first($options);
    }
    $current = (string) $current;

    $items = collect($options)
        ->map(fn ($text, $value) => ['value' => (string) $value, 'label' => $text])
        ->values();

    $hasError = $errors->has($name);

    $variants = [
        'light' => [
            'label'        => 'text-slate-600',
            'star'         => 'text-rose-500',
            'button'       => 'focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 disabled:opacity-75 disabled:bg-slate-100/50',
            'open'         => 'bg-white border-emerald-500 ring-4 ring-emerald-500/10',
            'idle'         => 'bg-slate-50/50 focus:bg-white',
            'border'       => 'border-slate-200 hover:border-slate-300',
            'borderError'  => 'border-rose-400 hover:border-rose-500',
            'value'        => 'text-slate-700',
            'placeholder'  => 'text-slate-400',
            'chevron'      => 'text-slate-400',
            'chevronOpen'  => 'rotate-180',
            'list'         => 'bg-white border-slate-200 shadow-lg shadow-slate-900/10',
            'optionIdle'   => 'text-slate-700',
            'optionActive' => 'bg-emerald-50 text-emerald-700',
            'check'        => 'text-emerald-500',
            'error'        => 'text-rose-500',
        ],
        'dark' => [
            'label'        => 'text-slate-300',
            'star'         => 'text-rose-400',
            'button'       => 'focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 disabled:opacity-60 disabled:bg-slate-800',
            'open'         => 'bg-transparent/20 border-emerald-500 ring-4 ring-emerald-500/20',
            'idle'         => 'bg-transparent/20 focus:bg-transparent',
            'border'       => 'border-slate-700 hover:border-slate-600',
            'borderError'  => 'border-rose-500 hover:border-rose-400',
            'value'        => 'text-slate-100',
            'placeholder'  => 'text-slate-500',
            'chevron'      => 'text-slate-500',
            'chevronOpen'  => 'rotate-180 text-slate-400',
            'list'         => 'bg-transparent/90 border-slate-700 shadow-xl shadow-black/40',
            'optionIdle'   => 'text-slate-200',
            'optionActive' => 'bg-emerald-500/15 text-emerald-300',
            'check'        => 'text-emerald-400',
            'error'        => 'text-rose-400',
        ],
        'glass' => [
            'label'        => 'text-white/80',
            'star'         => 'text-rose-400',
            'button'       => 'focus:border-white/40 focus:ring-4 focus:ring-white/10 disabled:opacity-50 disabled:bg-white/5',
            'open'         => 'bg-white/15 border-white/40 ring-4 ring-white/10',
            'idle'         => 'bg-white/10 focus:bg-white/15',
            'border'       => 'border-white/20 hover:border-white/40',
            'borderError'  => 'border-rose-400/70 hover:border-rose-400',
            'value'        => 'text-white',
            'placeholder'  => 'text-white/50',
            'chevron'      => 'text-white/60',
            'chevronOpen'  => 'rotate-180 text-white',
            'list'         => 'bg-neutral-900/90 backdrop-blur-xl border-white/15 shadow-2xl shadow-black/40 [scrollbar-width:thin] [scrollbar-color:rgba(255,255,255,0.25)_transparent]',
            'optionIdle'   => 'text-white/80',
            'optionActive' => 'bg-white/15 text-white',
            'check'        => 'text-emerald-400',
            'error'        => 'text-rose-400',
        ],
    ];

    $t = $variants[$variant] ?? $variants['light'];
@endphp

<div
    id="{{ $id }}-field"
    x-data="{
        options: @js($items),
        placeholder: @js($placeholder),
        emptyText: @js($emptyText),
        hasError: @js($hasError),
        value: @js($current),
        initial: @js($current),
        open: false,
        active: 0,

        get selected() {
            return this.options.find(o => o.value === this.value)
        },
        show() {
            if (!this.options.length) return
            this.active = Math.max(0, this.options.findIndex(o => o.value === this.value))
            this.open = true
            this.scrollToActive()
        },
        close() {
            this.open = false
        },
        toggle() {
            this.open ? this.close() : this.show()
        },
        move(step) {
            const n = this.options.length
            if (!n) return
            this.active = (this.active + step + n) % n
            this.scrollToActive()
        },
        choose(index) {
            const option = this.options[index]
            if (!option) return
            this.value = option.value
            this.close()
            this.$refs.button.focus()
        },
        scrollToActive() {
            this.$nextTick(() => {
                this.$refs.list.querySelectorAll('[role=option]')[this.active]?.scrollIntoView({ block: 'nearest' })
            })
        },
    }"
    x-modelable="value"
    x-init="
        $watch('value', () => $nextTick(() => $refs.input.dispatchEvent(new Event('change', { bubbles: true }))));
        $el.closest('form')?.addEventListener('reset', () => setTimeout(() => value = initial));

        Object.defineProperty($refs.input, 'value', {
            configurable: true,
            get: function() { return this.getAttribute('value') || ''; },
            set: function(val) {
                this.setAttribute('value', val);
                value = String(val);
            }
        });
    "
    @select-set="value = String($event.detail)"
    @click.outside="close()"
    @keydown.escape="if (open) { $event.stopPropagation(); close(); $refs.button.focus() }"
    {{ $attributes->merge(['class' => 'space-y-1.5']) }}
>
    @if ($label)
        <label for="{{ $id }}-button" class="block {{ $t['label'] }} text-[13px] font-medium max-[500px]:text-[12px]">
            {{ $label }}
            @if ($required)
                <span class="{{ $t['star'] }}">*</span>
            @endif
        </label>
    @endif

    {{-- Значение, которое уйдёт в форму --}}
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" x-ref="input" :value="value" value="{{ $current }}">

    <div class="relative">
        <button
            type="button"
            id="{{ $id }}-button"
            x-ref="button"
            @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
            aria-haspopup="listbox"
            aria-controls="{{ $id }}-listbox"
            aria-required="{{ $required ? 'true' : 'false' }}"
            :aria-expanded="open"
            :aria-activedescendant="open ? '{{ $id }}-option-' + active : null"
            :disabled="!options.length"
            class="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-left text-sm border rounded-xl focus:outline-none transition-all duration-200 disabled:cursor-not-allowed cursor-pointer select-none {{ $t['button'] }}"
            :class="open
                ? @js($t['open'])
                : [@js($t['idle']), hasError ? @js($t['borderError']) : @js($t['border'])]"
            @click="toggle()"
            @keydown.arrow-down.prevent="open ? move(1) : show()"
            @keydown.arrow-up.prevent="open ? move(-1) : show()"
            @keydown.home.prevent="open && (active = 0, scrollToActive())"
            @keydown.end.prevent="open && (active = options.length - 1, scrollToActive())"
            @keydown.enter.prevent="open ? choose(active) : show()"
            @keydown.space.prevent="open ? choose(active) : show()"
            @keyup.space.prevent
            @keydown.tab="close()"
        >
            <span
                class="block truncate"
                :class="selected ? @js($t['value']) : @js($t['placeholder'])"
                x-text="options.length ? (selected ? selected.label : placeholder) : emptyText"
            ></span>
            <i
                class="fas fa-chevron-down {{ $t['chevron'] }} text-[10px] shrink-0 transition-transform duration-200"
                :class="open && @js($t['chevronOpen'])"
            ></i>
        </button>

        <ul
            id="{{ $id }}-listbox"
            x-ref="list"
            x-show="open"
            style="display: none"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            role="listbox"
            tabindex="-1"
            class="absolute z-30 mt-1.5 w-full max-h-60 overflow-auto py-1.5 border rounded-xl focus:outline-none {{ $t['list'] }}"
        >
            <template x-for="(option, index) in options" :key="option.value">
                <li
                    role="option"
                    :id="'{{ $id }}-option-' + index"
                    :aria-selected="option.value === value"
                    class="mx-1.5 px-3 py-2 flex items-center justify-between gap-3 rounded-lg text-sm cursor-pointer transition-colors"
                    :class="[
                        active === index ? @js($t['optionActive']) : @js($t['optionIdle']),
                        option.value === value && 'font-medium',
                    ]"
                    @click="choose(index)"
                    @mousemove="if (active !== index) active = index"
                >
                    <span class="truncate" x-text="option.label"></span>
                    <i x-show="option.value === value" class="fas fa-check {{ $t['check'] }} text-[10px] shrink-0"></i>
                </li>
            </template>
        </ul>
    </div>

    @error($name)
        <p class="{{ $t['error'] }} text-xs">{{ $message }}</p>
    @enderror
</div>

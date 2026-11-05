{{-- resources/views/components/ui/select.blade.php --}}
@props([
    'options'     => [],
    'selected'    => null,
    'id'          => null,
    'name'        => null,        // необязательно: нужен, только если значение должно уйти в форму
    'placeholder' => null,
    'emptyText'   => 'Список пуст',
    'ariaLabel'   => null,
    'variant'     => 'glass',     // glass | light
])

@php
    $id = $id ?? $name ?? 'select-' . \Illuminate\Support\Str::random(6);

    $current = $selected instanceof \BackedEnum ? $selected->value : $selected;
    if ($current === null || $current === '') {
        $current = $placeholder ? '' : (string) array_key_first($options);
    }
    $current = (string) $current;

    $items = collect($options)
        ->map(fn ($text, $value) => ['value' => (string) $value, 'label' => $text])
        ->values();

    $styles = [
        'glass' => [
            'button'      => 'bg-white/10 border-white/20 text-white backdrop-blur-md focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/20 disabled:bg-white/5',
            'idle'        => 'hover:bg-white/15 hover:border-white/30',
            'open'        => 'bg-white/20 border-emerald-400 ring-4 ring-emerald-400/20',
            'text'        => 'text-white',
            'placeholder' => 'text-white/60',
            'chevron'     => 'text-white/60',
        ],
        'light' => [
            'button'      => 'bg-slate-50/50 border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 disabled:bg-slate-100/50',
            'idle'        => 'hover:border-slate-300',
            'open'        => 'bg-white border-emerald-500 ring-4 ring-emerald-500/10',
            'text'        => 'text-slate-700',
            'placeholder' => 'text-slate-400',
            'chevron'     => 'text-slate-400',
        ],
    ];
    $s = $styles[$variant] ?? $styles['glass'];
@endphp

<div
    id="{{ $id }}-field"
    x-data="{
        options: @js($items),
        placeholder: @js($placeholder),
        emptyText: @js($emptyText),
        value: @js($current),
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
            const changed = option.value !== this.value
            this.value = option.value
            this.close()
            this.$refs.button.focus()
            if (changed) {
                this.$nextTick(() => this.$refs.input.dispatchEvent(new Event('change', { bubbles: true })))
            }
        },
        scrollToActive() {
            this.$nextTick(() => {
                this.$refs.list.querySelectorAll('[role=option]')[this.active]?.scrollIntoView({ block: 'nearest' })
            })
        },
    }"
    x-modelable="value"
   x-init="
    Object.defineProperty($refs.input, 'value', {
        get: function() { return this.getAttribute('value') || ''; },
        set: function(val) {
            this.setAttribute('value', val);
            value = String(val);
        }
    });
"
    @select-set="value = String($event.detail)"
    @click.outside="close()"
    @keydown.escape.stop="open && (close(), $refs.button.focus())"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <input
        type="hidden"
        id="{{ $id }}"
        @if ($name) name="{{ $name }}" @endif
        x-ref="input"
        :value="value"
        value="{{ $current }}"
    >

    <button
        type="button"
        id="{{ $id }}-button"
        x-ref="button"
        aria-haspopup="listbox"
        aria-controls="{{ $id }}-listbox"
        @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
        :aria-expanded="open"
        :aria-activedescendant="open ? '{{ $id }}-option-' + active : null"
        :disabled="!options.length"
        class="w-full px-3 py-2 flex items-center justify-between gap-3 text-left text-sm border rounded-lg focus:outline-none transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed cursor-pointer select-none {{ $s['button'] }}"
        :class="open ? '{{ $s['open'] }}' : '{{ $s['idle'] }}'"
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
            :class="selected ? '{{ $s['text'] }}' : '{{ $s['placeholder'] }}'"
            x-text="options.length ? (selected ? selected.label : placeholder) : emptyText"
        ></span>
        <i
            class="fas fa-chevron-down text-[10px] shrink-0 transition-transform duration-200 {{ $s['chevron'] }}"
            :class="open && 'rotate-180'"
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
        class="absolute z-30 mt-1.5 w-full max-h-60 overflow-auto py-1.5 bg-white border border-slate-200 rounded-xl shadow-lg shadow-slate-900/10 focus:outline-none"
    >
        <template x-for="(option, index) in options" :key="option.value">
            <li
                role="option"
                :id="'{{ $id }}-option-' + index"
                :aria-selected="option.value === value"
                class="mx-1.5 px-3 py-2 flex items-center justify-between gap-3 rounded-lg text-sm text-slate-700 cursor-pointer"
                :class="{
                    'bg-emerald-50 text-emerald-700': active === index,
                    'font-medium': option.value === value,
                }"
                @click="choose(index)"
                @mousemove="if (active !== index) active = index"
            >
                <span class="truncate" x-text="option.label"></span>
                <i x-show="option.value === value" class="fas fa-check text-emerald-500 text-[10px] shrink-0"></i>
            </li>
        </template>
    </ul>
</div>

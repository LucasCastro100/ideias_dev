@props([
    'model' => '',
    'options' => [],
    'placeholder' => 'Selecione...',
    'id' => null,
    'searchPlaceholder' => 'Buscar...',
    'initial' => '',
])

@php
    $selectedOption = collect($options)->firstWhere('value', $initial);
    $selectedDisplay = $selectedOption ? $selectedOption['label'] : '';
@endphp

<div
    x-data="{
        options: @js($options),
        open: false,
        search: '',
        selectedId: @js($initial),
        selectedDisplay: @js($selectedDisplay),
        get filtered() {
            if (!this.search) return this.options;
            return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
        },
        select(option) {
            this.selectedId = option.value;
            this.selectedDisplay = option.label;
            this.search = '';
            this.open = false;
            $wire.set('{{ $model }}', option.value);
        },
        clear() {
            this.selectedId = '';
            this.selectedDisplay = '';
            $wire.set('{{ $model }}', '');
        }
    }"
    @if($id) id="{{ $id }}" @endif
    class="relative"
>
    <button type="button" @click="open = !open; $nextTick(() => $refs.searchInput.focus())"
        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-left flex items-center justify-between hover:border-gray-600 transition">
        <span x-text="selectedDisplay || '{{ $placeholder }}'" :class="selectedDisplay ? 'text-gray-200' : 'text-gray-500'"></span>
        <svg class="w-4 h-4 text-gray-400 transition shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" @click.away="open = false"
        class="absolute z-50 mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg shadow-xl overflow-hidden">
        <div class="p-2">
            <input x-ref="searchInput" type="text" x-model="search"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded px-3 py-1.5 text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                autocomplete="off">
        </div>
        <ul class="max-h-48 overflow-y-auto">
            <template x-for="option in filtered" :key="option.value">
                <li @click="select(option)" :class="option.value === selectedId ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700'"
                    class="px-3 py-2 cursor-pointer text-sm transition" x-text="option.label"></li>
            </template>
            <template x-if="filtered.length === 0">
                <li class="px-3 py-2 text-gray-500 text-sm text-center">Nenhum resultado encontrado</li>
            </template>
        </ul>
    </div>
</div>

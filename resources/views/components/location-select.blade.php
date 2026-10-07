@props(['regions', 'filteredStates', 'filteredCities'])

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm text-gray-300 mb-1">Região</label>
        <select wire:model.live="locRegion" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Selecione</option>
            @foreach ($regions as $r)
                <option value="{{ $r['nome'] }}">{{ $r['nome'] }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-300 mb-1">Estado</label>
        <select wire:model.live="locState" @if(empty($filteredStates)) disabled @endif
            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <option value="">Selecione</option>
            @foreach ($filteredStates as $s)
                <option value="{{ $s['nome'] }}">{{ $s['nome'] }} ({{ $s['sigla'] }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-300 mb-1">Município</label>
        <select wire:model.live="locCity" @if(empty($filteredCities)) disabled @endif
            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <option value="">Selecione</option>
            @foreach ($filteredCities as $c)
                <option value="{{ $c['nome'] }}">{{ $c['nome'] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
{{-- Modal para cadastrar eventos --}}
@if ($showEventModal)
    <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto text-center space-y-6">
            <h2 class="text-xl font-bold mb-4 text-gray-100">Cadastrar Evento</h2>

            <div class="space-y-4 text-left">
                <fieldset class="border border-gray-700 rounded p-4 mt-4">
                    <legend class="font-semibold mb-2 text-gray-300">Dados Evento</legend>
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-300">Nome do evento</label>
                        <input type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2" placeholder="Nome do evento"
                            wire:model.defer="eventName">
                        @error('eventName')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="block font-semibold text-gray-300">Data do evento</label>
                        <input type="date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2" wire:model.defer="eventDate" required>
                        @error('eventDate')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block font-semibold text-gray-300">Tipo do Evento</label>
                        <select wire:model.defer="tipoEvento"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                            <option value="interno">Interno</option>
                            <option value="regional">Regional</option>
                            <option value="nacional">Nacional</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="border border-gray-700 rounded p-4">
                    <legend class="font-semibold mb-2 text-gray-300">Localização</legend>

                    <div class="mb-4">
                        <label class="block font-semibold text-gray-300">Região</label>
                        <input list="regions" wire:model.lazy="selectedRegion"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2" placeholder="Digite ou selecione a região" />
                        <datalist id="regions">
                            @foreach ($regions as $region)
                                <option value="{{ $region['nome'] }}"></option>
                            @endforeach
                        </datalist>

                        <div wire:loading.delay wire:target="selectedRegion" wire:loading.class='w-full p-2'>
                            <span class="font-semibold text-gray-400">Carregando Estados...</span>
                        </div>
                    </div>

                    @if ($selectedRegionId)
                        <div class="mb-4">
                            <label class="block font-semibold text-gray-300">Estado</label>

                            @if (count($filteredStates) > 0)
                                <input list="states" wire:model.lazy="selectedState"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2"
                                    placeholder="Digite ou selecione o estado" />
                                <datalist id="states">
                                    @foreach ($filteredStates as $state)
                                        <option value="{{ $state['nome'] }}"></option>
                                    @endforeach
                                </datalist>
                            @endif

                            <div wire:loading.delay wire:target="selectedState" wire:loading.class='w-full p-2'>
                                <span class="font-semibold text-gray-400">Carregando Municípios...</span>
                            </div>
                        </div>
                    @endif

                    @if ($selectedStateId)
                        <div class="mb-0">
                            <label class="block font-semibold text-gray-300">Município</label>

                            @if (count($filteredCities) > 0)
                                <input list="cities" wire:model.lazy="selectedCity"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2"
                                    placeholder="Digite ou selecione o município" />
                                <datalist id="cities">
                                    @foreach ($filteredCities as $city)
                                        <option value="{{ $city['nome'] }}"></option>
                                    @endforeach
                                </datalist>
                            @endif
                        </div>
                    @endif
                </fieldset>

            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button wire:click="closeEventModal"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded transition">Fechar</button>
                <button wire:click="saveEvent"
                    class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded transition">Salvar</button>
            </div>

            <div wire:loading wire:target="saveEvent" wire:loading.class='w-full p-2'>
                <span class="font-semibold text-gray-400">Salvando evento...</span>
            </div>
        </div>
    </div>
@endif

{{-- Modal para cadastrar equipes --}}
@if ($showTeamModal)
    <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
        <div
            class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto text-center sm:max-w-xl md:max-w-2xl">
            <h2 class="text-xl font-bold mb-6 text-gray-100">Cadastrar Equipe</h2>

            <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
                <div class="mb-6 text-left lg:col-span-2">
                    <label for="selectedEventId" class="block font-semibold text-gray-300 mb-1">Selecione o Evento</label>
                    <div x-data="{
                        events: @js($events),
                        open: false,
                        search: '',
                        selectedId: @js($selectedEventId),
                        selectedDisplay: @js($selectedEventId ? collect($events)->firstWhere('id', $selectedEventId)['display'] ?? '' : ''),
                        get filtered() {
                            if (!this.search) return this.events;
                            return this.events.filter(e => e.display.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        select(event) {
                            this.selectedId = event.id;
                            this.selectedDisplay = event.display;
                            this.search = '';
                            this.open = false;
                            $wire.set('selectedEventId', event.id);
                        },
                        clear() {
                            this.selectedId = '';
                            this.selectedDisplay = '';
                            $wire.set('selectedEventId', '');
                        }
                    }" class="relative">
                        <button type="button" @click="open = !open; $nextTick(() => $refs.searchInput.focus())"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-left flex items-center justify-between hover:border-gray-600 transition">
                            <span x-text="selectedDisplay || '-- Selecione um evento --'" :class="selectedDisplay ? 'text-gray-200' : 'text-gray-500'"></span>
                            <svg class="w-4 h-4 text-gray-400 transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute z-50 mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg shadow-xl overflow-hidden">
                            <div class="p-2">
                                <input x-ref="searchInput" type="text" x-model="search"
                                    placeholder="Buscar evento..."
                                    class="w-full bg-gray-700 border border-gray-600 text-gray-200 rounded px-3 py-1.5 text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    autocomplete="off">
                            </div>
                            <ul class="max-h-48 overflow-y-auto">
                                <template x-for="event in filtered" :key="event.id">
                                    <li @click="select(event)" :class="event.id === selectedId ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700'"
                                        class="px-3 py-2 cursor-pointer text-sm transition" x-text="event.display"></li>
                                </template>
                                <template x-if="filtered.length === 0">
                                    <li class="px-3 py-2 text-gray-500 text-sm text-center">Nenhum evento encontrado</li>
                                </template>
                            </ul>
                        </div>
                        <template x-if="selectedId">
                            <button type="button" @click="clear()" class="absolute right-8 top-2.5 text-gray-500 hover:text-gray-300 text-sm">&times;</button>
                        </template>
                    </div>
                    @error('selectedEventId')
                        <span class="text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6 text-left">
                    <label for="numTeams" class="block font-semibold text-gray-300 mb-1">Quantidade de equipes</label>
                    <input type="number" id="numTeams" min="1" wire:model.lazy="numTeams"
                        class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 w-full" />
                </div>
            </div>

            <form wire:submit.prevent="saveTeams" class="space-y-8">
                @foreach ($teams as $index => $team)
                    <div class="border border-gray-700 rounded p-4 space-y-3">
                        <div class="grid gap-4 grid-cols-1 md:grid-cols-3 items-start">
                            <div class="md:col-span-2">
                                <label for="teamName{{ $index }}" class="block font-semibold text-gray-300 mb-1">Nome da equipe <span class="text-red-400">*</span></label>
                                <input id="teamName{{ $index }}"
                                    wire:model.defer="teams.{{ $index }}.name"
                                    placeholder="Nome da equipe"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                @error("teams.$index.name")
                                    <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="teamCategory{{ $index }}"
                                    class="block font-semibold text-gray-300 mb-1">Categoria</label>
                                <select id="teamCategory{{ $index }}"
                                    wire:model.defer="teams.{{ $index }}.category"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['slug'] }}">{{ ucfirst($category['slug']) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("teams.$index.category")
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <details class="border-t border-gray-700 pt-3">
                            <summary class="text-sm text-gray-400 cursor-pointer hover:text-gray-200 transition font-medium">
                                Representante (opcional)
                            </summary>
                            <div class="grid gap-4 grid-cols-1 md:grid-cols-3 mt-3">
                                <div class="md:col-span-3">
                                    <label for="repUser{{ $index }}" class="block font-semibold text-gray-300 mb-1">Selecionar usuário</label>
                                    <select id="repUser{{ $index }}"
                                        wire:change="fillTeamRepresentative({{ $index }}, $event.target.value)"
                                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Nenhum --</option>
                                        @foreach ($allUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="repName{{ $index }}" class="block font-semibold text-gray-300 mb-1">Nome</label>
                                    <input id="repName{{ $index }}"
                                        wire:model.defer="teams.{{ $index }}.representative_name"
                                        placeholder="Nome do representante"
                                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                    @error("teams.$index.representative_name")
                                        <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="repEmail{{ $index }}" class="block font-semibold text-gray-300 mb-1">E-mail</label>
                                    <input id="repEmail{{ $index }}" type="email"
                                        wire:model.defer="teams.{{ $index }}.representative_email"
                                        placeholder="email@exemplo.com"
                                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                    @error("teams.$index.representative_email")
                                        <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="repPhone{{ $index }}" class="block font-semibold text-gray-300 mb-1">Telefone</label>
                                    <input id="repPhone{{ $index }}" type="tel" maxlength="15"
                                        wire:model.defer="teams.{{ $index }}.representative_phone"
                                        placeholder="(99) 99999-9999"
                                        x-on:input="
                                            let val = $event.target.value.replace(/\D/g, '').substring(0, 11);
                                            val = val.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
                                            $event.target.value = val"
                                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                    @error("teams.$index.representative_phone")
                                        <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </details>
                    </div>
                @endforeach

                <div class="flex flex-wrap gap-2 justify-center mt-6">
                    <button wire:click="closeTeamModal"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded transition">
                        Fechar
                    </button>

                    <button type="button" wire:click="resetTeamForm"
                        class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded transition">
                        Limpar Formulário
                    </button>

                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded transition">
                        Cadastrar Equipes
                    </button>
                </div>

                <div wire:loading wire:loading.target="saveTeams" wire:loading.class='w-full p-2'>
                    <span class="font-semibold text-gray-400">Salvando equipes...</span>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- Modal de confirmação para limpar armazenamento --}}
@if ($confirmClearStorage)
    <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md text-center">
            <h2 class="text-xl font-bold mb-4 text-gray-100">Tem certeza que deseja apagar todos os dados?</h2>
            <p class="mb-6 text-gray-400">Essa ação irá limpar todos os eventos e equipes salvos.</p>

            <div class="flex justify-center space-x-4">
                <button wire:click="cancelClearStorage"
                    class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-semibold py-2 px-4 rounded transition">Cancelar</button>

                <button wire:click="confirmAndClearStorage"
                    class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded transition">Apagar Dados</button>
            </div>
        </div>
    </div>
@endif
</div>

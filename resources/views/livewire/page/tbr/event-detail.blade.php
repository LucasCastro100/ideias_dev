<div>
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="bg-green-600 text-white p-3 rounded-lg mb-4 transition duration-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('tbr.dashboard') }}"
                class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-100">{{ $event?->name ?? 'Evento não encontrado' }}</h1>
                @if ($event)
                    <span class="text-sm text-gray-400 mt-1 block">
                        {{ $event->teams_count }} equipe(s) cadastrada(s)
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('create-event')
                <button wire:click="openEditModal"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition text-sm">
                    <i class="fas fa-pen mr-1"></i> Editar Evento
                </button>
            @endcan
            @can('delete')
                <button wire:click="openDeleteModal"
                    class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition text-sm">
                    <i class="fas fa-trash mr-1"></i> Excluir Evento
                </button>
            @endcan
        </div>
    </div>

    @if (!$event)
        <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
            <span class="block font-bold">Evento não encontrado.</span>
        </div>
    @else
        <div class="grid gap-6 grid-cols-1 lg:grid-cols-3 mb-6">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Informações</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 text-sm">
                    <div>
                        <span class="text-gray-500 block">Data</span>
                        <span class="text-gray-200">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Local</span>
                        @if ($event->location && ($event->location['municipio'] ?? null))
                            <span class="text-gray-200">{{ $event->location['municipio']['nome'] }} -
                                {{ $event->location['estado']['sigla'] ?? '' }}</span>
                        @else
                            <span class="text-gray-500">Não informado</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-500 block">Tipo</span>
                        @php
                            $tipo = $event->tipo_evento ?? 'interno';
                            $badgeColors = [
                                'interno' => 'bg-gray-600',
                                'regional' => 'bg-blue-600',
                                'nacional' => 'bg-green-600',
                            ];
                        @endphp
                        <span
                            class="inline-block text-xs font-bold uppercase tracking-wide px-2 py-0.5 rounded {{ $badgeColors[$tipo] ?? 'bg-gray-600' }} text-white">{{ ucfirst($tipo) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Status</span>
                        @if ($event->status)
                            <span class="text-green-400 font-semibold">Concluído</span>
                        @else
                            <span class="text-blue-400 font-semibold">Em andamento</span>
                        @endif
                    </div>

                </div>
            </div>

            <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-xl p-5">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Ações</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <a href="{{ route('tbr.ranking', ['event_id' => $event->id]) }}" target="_blank"
                        class="flex flex-col items-center justify-center gap-2 p-4 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-center">
                        <i class="fas fa-trophy text-2xl text-yellow-500"></i>
                        <span class="text-sm text-gray-200 font-medium">Ranking</span>
                    </a>
                    <a href="{{ route('tbr.event-teams', ['event_id' => $event->id]) }}" target="_blank"
                        class="flex flex-col items-center justify-center gap-2 p-4 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-center">
                        <i class="fas fa-users text-2xl text-blue-400"></i>
                        <span class="text-sm text-gray-200 font-medium">Equipes</span>
                    </a>
                    @can('create-event')
                        <a href="{{ route('tbr.edit-scores', ['event_id' => $event->id]) }}"
                            class="flex flex-col items-center justify-center gap-2 p-4 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-center">
                            <i class="fas fa-pen-to-square text-2xl text-purple-400"></i>
                            <span class="text-sm text-gray-200 font-medium">Editar Notas</span>
                        </a>
                    @endcan

                    <a href="{{ route('tbr.link', ['event_id' => $event->id]) }}" target="_blank"
                        class="flex flex-col items-center justify-center gap-2 p-4 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-center">
                        <i class="fas fa-link text-2xl text-green-400"></i>
                        <span class="text-sm text-gray-200 font-medium">Links</span>
                    </a>
                    <a href="{{ route('tbr.slide', ['event_id' => $event->id]) }}" target="_blank"
                        class="flex flex-col items-center justify-center gap-2 p-4 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-center">
                        <i class="fas fa-display text-2xl text-cyan-400"></i>
                        <span class="text-sm text-gray-200 font-medium">Slide</span>
                    </a>
                    @can('create-event')
                        <button wire:click="openExportModal"
                            class="flex flex-col items-center justify-center gap-2 p-4 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-center">
                            <i class="fas fa-file-lines text-2xl text-orange-400"></i>
                            <span class="text-sm text-gray-200 font-medium">Exportar Notas</span>
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Edit Modal --}}
        @if ($showEditModal)
            <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
                <form wire:submit.prevent="updateEvent"
                    class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto text-center space-y-6">
                    <h2 class="text-xl font-bold text-gray-100 mb-4">Editar Evento</h2>

                    <div class="space-y-4 text-left">
                        <fieldset class="border border-gray-700 rounded-xl p-4">
                            <legend class="font-semibold text-gray-300 mb-2 px-2">Dados Evento</legend>
                            <div class="mb-4">
                                <label class="block font-semibold text-gray-300 text-sm mb-1">Nome do evento</label>
                                <input type="text" wire:model.defer="editEventName"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nome do evento">
                                @error('editEventName')
                                    <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block font-semibold text-gray-300 text-sm mb-1">Data do evento</label>
                                <input type="date" wire:model.defer="editEventDate"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                @error('editEventDate')
                                    <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-300 text-sm mb-1">Tipo do Evento</label>
                                <select wire:model="editTipoEvento"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="interno">Interno</option>
                                    <option value="regional">Regional</option>
                                    <option value="nacional">Nacional</option>
                                </select>
                            </div>

                            <div class="mt-4">
                                <label class="block font-semibold text-gray-300 text-sm mb-2">Status do Evento</label>
                                <div class="flex gap-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" wire:model="editEventStatus" value="0"
                                            class="form-radio text-blue-500 bg-gray-800 border-gray-600">
                                        <span class="ml-2 text-blue-400 font-semibold">Em andamento</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" wire:model="editEventStatus" value="1"
                                            class="form-radio text-green-500 bg-gray-800 border-gray-600">
                                        <span class="ml-2 text-green-400 font-semibold">Concluído</span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="border border-gray-700 rounded-xl p-4">
                            <legend class="font-semibold text-gray-300 mb-2 px-2">Localização</legend>
                            <div class="mb-4">
                                <label class="block font-semibold text-gray-300 text-sm mb-1">Região</label>
                                <input list="editRegions" wire:model="editSelectedRegion"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Digite ou selecione a região" />
                                <datalist id="editRegions">
                                    @foreach ($regions as $region)
                                        <option value="{{ $region['nome'] }}"></option>
                                    @endforeach
                                </datalist>
                            </div>

                            @if ($editSelectedRegionId)
                                <div class="mb-4">
                                    <label class="block font-semibold text-gray-300 text-sm mb-1">Estado</label>
                                    @if (count($filteredEditStates) > 0)
                                        <input list="editStates" wire:model="editSelectedState"
                                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                            placeholder="Digite ou selecione o estado" />
                                        <datalist id="editStates">
                                            @foreach ($filteredEditStates as $state)
                                                <option value="{{ $state['nome'] }}"></option>
                                            @endforeach
                                        </datalist>
                                    @endif
                                </div>
                            @endif

                            @if ($editSelectedStateId)
                                <div>
                                    <label class="block font-semibold text-gray-300 text-sm mb-1">Município</label>
                                    @if (count($filteredEditCities) > 0)
                                        <input list="editCities" wire:model="editSelectedCity"
                                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                            placeholder="Digite ou selecione o município" />
                                        <datalist id="editCities">
                                            @foreach ($filteredEditCities as $city)
                                                <option value="{{ $city['nome'] }}"></option>
                                            @endforeach
                                        </datalist>
                                    @endif
                                </div>
                            @endif
                        </fieldset>

                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeEditModal"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">Fechar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition">Atualizar</button>
                    </div>

                    <div wire:loading wire:target="updateEvent" class="w-full p-2">
                        <span class="text-blue-400 font-semibold">Atualizando evento...</span>
                    </div>
                </form>
            </div>
        @endif

        {{-- Export Modal --}}
        @if ($showExportModal)
            <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 w-full max-w-md">
                    <h2 class="text-xl font-bold text-gray-100 mb-4">Exportar Notas</h2>
                    <p class="text-sm text-gray-400 mb-4">Selecione as modalidades que deseja incluir no PDF:</p>

                    <div class="space-y-3 mb-6">
                        @foreach ($exportAvailableModalities as $mod)
                            <label class="flex items-center gap-3 p-3 bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-700 transition">
                                <input type="checkbox" value="{{ $mod['slug'] }}"
                                    wire:model.live="exportSelectedModalities"
                                    class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded">
                                <span class="text-gray-200 text-sm font-medium">{{ $mod['label'] }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if (empty($exportSelectedModalities))
                        <p class="text-red-400 text-sm mb-4">Selecione pelo menos uma modalidade.</p>
                    @endif

                    <div class="border-t border-gray-700 pt-4 mb-4 space-y-2">
                        <p class="text-sm text-gray-400 font-medium mb-2">Informações na capa do PDF:</p>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="exportPdfShowPosition"
                                class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded">
                            <span class="text-gray-300 text-sm">Mostrar posição no ranking</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="exportPdfShowTotal"
                                class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded">
                            <span class="text-gray-300 text-sm">Mostrar nota total</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="exportPdfShowModalities"
                                class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded">
                            <span class="text-gray-300 text-sm">Mostrar páginas de modalidades</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="closeExportModal"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">Cancelar</button>
                        <button type="button" wire:click="exportScores"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white rounded-lg transition">
                            <i class="fas fa-file-export mr-1"></i> Exportar
                        </button>
                    </div>

                    <div wire:loading wire:target="exportScores" class="w-full p-2 text-center">
                        <span class="text-orange-400 font-semibold">Preparando exportação...</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Delete Modal --}}
        @if ($showDeleteModal)
            <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 w-full max-w-md text-center">
                    <h2 class="text-xl font-bold mb-4 text-red-400">Confirmar Exclusão</h2>
                    <p class="mb-6 text-gray-300">Tem certeza que deseja apagar este evento?</p>

                    <div class="flex justify-center gap-3">
                        <button type="button" wire:click="closeDeleteModal"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">Cancelar</button>
                        <button type="button" wire:click="deleteEvent"
                            class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition">Apagar</button>
                    </div>

                    <div wire:loading wire:target="deleteEvent" class="w-full p-2 text-center">
                        <span class="text-red-400 font-semibold">Apagando evento...</span>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<div class="p-6">
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h1 class="text-2xl font-semibold text-gray-500">Dashboard</h1>
    </div>

    @if (count($events ?? []) === 0)

        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block font-bold">Nenhum evento cadastrado.</span>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($events as $event)
                <div class="border rounded-lg p-4 shadow hover:shadow-lg transition relative">
                    {{-- Ícone engrenagem no topo direito --}}
                    <div x-data="{ open: false }" class="absolute top-3 right-3 z-10">
                        <button @click="open = !open" class="text-gray-700 hover:text-gray-900" title="Ações do evento"
                            aria-label="Ações do evento">
                            <i class="fas fa-cog"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg z-20">
                            <ul class="py-1 text-sm text-gray-700">
                                <li>
                                    <button wire:click="openScoreModal('{{ $event['id'] }}')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                                        Pontuação
                                    </button>
                                </li>
                                <li>
                                    <a href="{{ route('tbr.ranking', ['event_id' => $event['id']]) }}"
                                        class="block px-4 py-2 hover:bg-gray-100">
                                        Notas
                                    </a>
                                </li>
                                <li>
                                    <button wire:click="openEditModal('{{ $event['id'] }}')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                                        Atualizar
                                    </button>
                                </li>
                                <li>
                                    <button wire:click="openDeleteModal('{{ $event['id'] }}')"
                                        class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                        Apagar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Conteúdo do card --}}
                    <h2 class="text-xl font-bold pr-6">{{ $event['nome'] }}</h2>
                    <p class="text-gray-600 mb-2">{{ \Carbon\Carbon::parse($event['data'])->format('d/m/Y') }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal pontuação -->
    @if ($showScoreModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg p-8 w-full max-w-2xl relative"
                wire:key="score-modal-{{ $selectedEventId }}">
                <h2 class="text-2xl font-bold mb-6 text-center">{{ $selectedEventName }}</h2>

                <div class="mb-4">
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Categoria</label>
                    <select wire:model.lazy="selectedCategory" id="category" class="w-full border rounded px-3 py-2">
                        <option value="">--- SELECIONE ---</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedCategory)
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @foreach ($modalities as $modality)
                            @php
                                $colorClass = match ($modality['slug']) {
                                    'mc' => 'bg-blue-600',
                                    'om' => 'bg-green-600',
                                    'te' => 'bg-yellow-600',
                                    'dp' => 'bg-red-600',
                                    default => 'bg-gray-600',
                                };
                            @endphp

                            <a href="{{ route('tbr.score', ['event_id' => $selectedEventId, 'category_id' => $selectedCategory, 'modality_id' => $modality['id']]) }}"
                                class="py-2 rounded text-center text-white hover:opacity-90 transition {{ $colorClass }}">
                                {{ $modality['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end">
                    <button wire:click="closeScoreModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Modal de edição -->
    @if ($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md text-center space-y-6">

                <h2 class="text-xl font-bold mb-4">Editar Evento</h2>

                <div class="space-y-4 text-left">

                    {{-- Nome do evento --}}
                    <label class="block font-semibold">Nome do evento</label>
                    <input type="text" class="w-full border rounded px-3 py-2" placeholder="Nome do evento"
                        wire:model.defer="editEventName">
                    @error('editEventName')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror

                    {{-- Data do evento --}}
                    <label class="block font-semibold">Data do evento</label>
                    <input type="date" class="w-full border rounded px-3 py-2" wire:model.defer="editEventDate">
                    @error('editEventDate')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror

                    {{-- Configuração do Ranking / PowerPoint --}}
                    <fieldset class="border border-gray-300 rounded p-4">
                        <legend class="font-semibold mb-2">Configuração do Ranking / Exportação</legend>

                        {{-- Modalidades --}}
                        <div class="mb-4">
                            <label class="block font-medium mb-1">Modalidades para exibir:</label>
                            <div class="flex flex-wrap gap-4">
                                @foreach (['ap' => 'AP', 'mc' => 'MC', 'om' => 'OM', 'te' => 'TE', 'dp' => 'DP'] as $key => $label)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" wire:model.defer="editRankingConfig.modalities_to_show"
                                            value="{{ $key }}"
                                            class="form-checkbox h-5 w-5 text-purple-600" />
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('editRankingConfig.modalities_to_show')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Número de posições por modalidade --}}
                        <div class="mb-4">
                            <label class="block font-medium mb-1">Número de posições por modalidade:</label>
                            <input type="number" min="1" max="3"
                                wire:model.defer="editRankingConfig.top_positions"
                                class="border rounded px-3 py-2 w-20" />
                            @error('editRankingConfig.top_positions')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Número de posições no ranking geral --}}
                        <div>
                            <label class="block font-medium mb-1">Número de posições no ranking geral:</label>
                            <input type="number" min="1" max="5"
                                wire:model.defer="editRankingConfig.general_top_positions"
                                class="border rounded px-3 py-2 w-20" />
                            @error('editRankingConfig.general_top_positions')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </fieldset>
                </div>

                <div class="text-right space-x-2 mt-6">
                    <button wire:click="updateEvent"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Atualizar</button>
                    <button wire:click="closeEditModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">Fechar</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de confirmação para apagar -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4 text-center text-red-600">Confirmação</h2>
                <p class="mb-4 text-center">Tem certeza que deseja apagar este evento?</p>

                <div class="flex justify-end gap-2">
                    <button wire:click="closeDeleteModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancelar</button>
                    <button wire:click="deleteEvent"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Apagar</button>
                </div>
            </div>
        </div>
    @endif
</div>

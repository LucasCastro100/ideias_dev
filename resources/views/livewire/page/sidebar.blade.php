<div>
    <div x-data="{ collapsed: false, showNav: true, showActions: true }" :class="collapsed ? 'w-16' : 'w-64'"
        class="bg-white border-r shadow-md transition-all duration-300 ease-in-out h-screen flex flex-col">

        {{-- Botão de colapsar --}}
        <div class="p-4">
            <button @click="collapsed = !collapsed" class="focus:outline-none text-gray-700 mx-auto"
                title="Expandir/Colapsar sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Navegação --}}
        <div class="px-2 text-gray-500 text-xs uppercase font-bold tracking-wide mt-2">
            <div class="flex items-center justify-between cursor-pointer" @click="showNav = !showNav"
                title="Mostrar/Esconder Navegação">
                <span x-show="!collapsed">Navegação</span>
                <svg x-show="!collapsed" :class="showNav ? 'rotate-180' : ''"
                    class="h-4 w-4 transform transition-transform" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <ul x-show="showNav" x-transition class="space-y-1 mt-2 text-gray-700">
                <li>
                    <a wire:navigate href="{{ route('tbr.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-100 transition"
                        :class="{ 'justify-center': collapsed }" title="Dashboard">
                        <i class="fas fa-house text-sm w-5 text-gray-600"></i>
                        <span x-show="!collapsed" class="uppercase">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <hr class="my-4 border-t border-gray-200" />

        {{-- Ações --}}
        <div class="px-2 text-gray-500 text-xs uppercase font-bold tracking-wide">
            <div class="flex items-center justify-between cursor-pointer" @click="showActions = !showActions"
                title="Mostrar/Esconder Ações">
                <span x-show="!collapsed">Ações</span>
                <svg x-show="!collapsed" :class="showActions ? 'rotate-180' : ''"
                    class="h-4 w-4 transform transition-transform" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <ul x-show="showActions" x-transition class="space-y-1 mt-2 text-gray-700">
                <li>
                    <button wire:click="openEventModal"
                        class="flex items-center gap-3 px-4 py-2 rounded w-full text-left transition hover:bg-green-100"
                        :class="{ 'justify-center': collapsed }" title="Abrir modal para cadastrar eventos">
                        <i class="fas fa-calendar-plus text-sm w-5 text-gray-600"></i>
                        <span x-show="!collapsed" class="uppercase">Cadastrar Eventos</span>
                    </button>
                </li>

                <li>
                    <button wire:click="openTeamModal"
                        class="flex items-center gap-3 px-4 py-2 rounded w-full text-left transition hover:bg-blue-100"
                        :class="{ 'justify-center': collapsed }" title="Abrir modal para cadastrar equipes">
                        <i class="fas fa-users text-sm w-5 text-gray-600"></i>
                        <span x-show="!collapsed" class="uppercase">Cadastrar Equipes</span>
                    </button>
                </li>

                <li>
                    <button wire:click="askToClearStorage"
                        class="flex items-center gap-3 px-4 py-2 rounded w-full text-left transition hover:bg-red-100"
                        :class="{ 'justify-center': collapsed }" title="Limpar todos os dados salvos">
                        <i class="fas fa-trash text-sm w-5 text-gray-600"></i>
                        <span x-show="!collapsed" class="uppercase">Limpar Dados</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- Modal para cadastrar eventos --}}
    @if ($showEventModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md text-center space-y-6">

                <h2 class="text-xl font-bold mb-4">Cadastrar Evento</h2>

                <div class="space-y-4 text-left">

                    {{-- Nome do evento --}}
                    <label class="block font-semibold">Nome do evento</label>
                    <input type="text" class="w-full border rounded px-3 py-2" placeholder="Nome do evento"
                        wire:model.defer="eventName">
                    @error('eventName')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror

                    {{-- Data do evento --}}
                    <label class="block font-semibold">Data do evento</label>
                    <input type="date" class="w-full border rounded px-3 py-2" wire:model.defer="eventDate">
                    @error('eventDate')
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
                                        <input type="checkbox" wire:model.defer="rankingConfig.modalities_to_show"
                                            value="{{ $key }}" class="form-checkbox h-5 w-5 text-purple-600" />
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Número de posições por modalidade --}}
                        <div class="mb-4">
                            <label class="block font-medium mb-1">Número de posições por modalidade:</label>
                            <input type="number" min="1" max="3"
                                wire:model.defer="rankingConfig.top_positions" class="border rounded px-3 py-2 w-20" />
                        </div>

                        {{-- Número de posições no ranking geral --}}
                        <div>
                            <label class="block font-medium mb-1">Número de posições no ranking geral:</label>
                            <input type="number" min="1" max="5"
                                wire:model.defer="rankingConfig.general_top_positions"
                                class="border rounded px-3 py-2 w-20" />
                        </div>
                    </fieldset>
                </div>

                <div class="text-right space-x-2 mt-6">
                    <button wire:click="saveEvent"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Salvar</button>
                    <button wire:click="closeEventModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">Fechar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal para cadastrar equipes --}}
    @if ($showTeamModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                class="bg-white p-6 rounded shadow w-full max-w-lg max-h-[90vh] overflow-y-auto text-center
           sm:max-w-xl md:max-w-2xl">

                <h2 class="text-xl font-bold mb-6">Cadastrar Equipe</h2>

                <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
                    {{-- Select de evento --}}
                    <div class="mb-6 text-left lg:col-span-2">
                        <label for="selectedEventIndex" class="block font-semibold mb-1">Selecione o Evento</label>
                        <select id="selectedEventIndex" wire:model="selectedEventIndex"
                            class="border rounded px-3 py-2 w-full">
                            <option value="">-- Selecione um evento --</option>
                            @foreach ($events as $index => $event)
                                <option value="{{ $index }}">{{ $event['nome'] }} ({{ $event['data'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('selectedEventIndex')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Quantidade de equipes --}}
                    <div class="mb-6 text-left">
                        <label for="numTeams" class="block font-semibold mb-1">Quantidade de equipes</label>
                        <input type="number" id="numTeams" min="1" wire:model.lazy="numTeams"
                            class="border rounded px-3 py-2 w-full" />
                    </div>
                </div>

                {{-- Formulário equipes --}}
                <form wire:submit.prevent="saveTeams" class="space-y-8">
                    @foreach ($teams as $index => $team)
                        <div
                            class="grid gap-4 grid-cols-1 md:grid-cols-3 items-center
                       border border-gray-300 rounded p-4
                       @media(max-width: 768px) { grid-template-columns: 1fr !important; }">

                            {{-- Nome da equipe --}}
                            <div class="col-span-3 md:col-span-2">
                                <label for="teamName{{ $index }}" class="block font-semibold mb-1">Nome da
                                    equipe</label>
                                <input type="text" id="teamName{{ $index }}"
                                    wire:model.defer="teams.{{ $index }}.name"
                                    class="border rounded px-3 py-2 w-full" />
                                @error("teams.$index.name")
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Categoria --}}
                            <div
                                class="col-span-3 md:col-span-1 mt-4 md:mt-0 md:pl-4 border-t md:border-t-0 md:border-l border-gray-300">
                                <label for="teamCategory{{ $index }}"
                                    class="block font-semibold mb-1">Categoria</label>
                                <select id="teamCategory{{ $index }}"
                                    wire:model.defer="teams.{{ $index }}.category"
                                    class="border rounded px-3 py-2 w-full">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['slug'] }}">{{ ucfirst($category['slug']) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("teams.$index.category")
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                    <div class="flex space-x-4 justify-center mt-6">
                        {{-- Envia dados das equipes --}}
                        <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                            Cadastrar Equipes
                        </button>

                        {{-- Apenas limpa o formulário de equipes (sem apagar o JSON) --}}
                        <button type="button" wire:click="resetTeamForm"
                            class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                            Limpar Formulário
                        </button>

                        <button wire:click="closeTeamModal"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">
                            Fechar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal de confirmação para limpar armazenamento --}}
    @if ($confirmClearStorage)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md text-center">
                <h2 class="text-xl font-bold mb-4">Tem certeza que deseja apagar todos os dados?</h2>
                <p class="mb-6 text-gray-700">Essa ação irá limpar todos os eventos e equipes salvos no JSON.</p>

                <div class="flex justify-center space-x-4">
                    <button wire:click="cancelClearStorage"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">Cancelar</button>

                    <button wire:click="confirmAndClearStorage"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Apagar Dados</button>
                </div>
            </div>
        </div>
    @endif
</div>

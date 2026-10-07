<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-100">Missões DP</h1>
        @can('create')
        <button wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Missão
        </button>
        @endcan
    </div>

    <div class="flex flex-wrap gap-4 mb-4">
        <select wire:model.live="filterYear"
            class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todos os anos</option>
            @foreach ($years as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterDpLevel"
            class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todos os níveis</option>
            <option value="baby">Baby</option>
            <option value="kids1">Kids 1</option>
            <option value="kids2">Kids 2</option>
            <option value="middle">Middle</option>
            <option value="high">High</option>
            <option value="technic_university">Technic / University</option>
        </select>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 ">Ano</th>
                    <th class="px-4 py-3 ">Nível</th>
                    <th class="px-4 py-3 ">Missão</th>
                    <th class="px-4 py-3 ">Descrição</th>
                    <th class="px-4 py-3 ">Depende de</th>
                    <th class="px-4 py-3 text-right w-24">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($missions as $m)
                    <tr class="hover:bg-gray-800/50 transition">
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-600 text-gray-200">
                                {{ $m->year }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-700 text-gray-300">
                                {{ $m->dp_level }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-100">{{ $m->mission_title }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ $m->description }}</td>
                        <td class="px-4 py-3">
                            @if ($m->depends_on)
                                <span class="text-amber-400 text-xs font-medium">{{ $m->depends_on }}</span>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('edit')
                            <button wire:click="openEditModal('{{ $m->id }}')"
                                class="text-blue-400 hover:text-blue-300 mr-3" title="Editar">
                                <i class="fas fa-pen"></i>
                            </button>
                            @endcan
                            @can('delete')
                            <button wire:click="openDeleteModal('{{ $m->id }}')"
                                class="text-red-400 hover:text-red-300" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8  text-gray-500">Nenhuma missão cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create Modal --}}
    @if ($showCreateModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <form wire:submit.prevent="save" class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-lg space-y-4 overflow-y-auto max-h-[85vh]">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Nova Missão DP</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Ano</label>
                    <input type="number" wire:model.defer="editYear" min="2020" max="2099"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                    @error('editYear') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Nível DP</label>
                    <select wire:model.defer="editDpLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="baby">Baby</option>
                        <option value="kids1">Kids 1</option>
                        <option value="kids2">Kids 2</option>
                        <option value="middle">Middle</option>
                        <option value="high">High</option>
                        <option value="technic_university">Technic / University</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Título da Missão</label>
                    <input type="text" wire:model.defer="editMissionTitle"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                    @error('editMissionTitle') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Descrição</label>
                    <input type="text" wire:model.defer="editDescription"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Depende de (opcional)</label>
                    <x-searchable-select model="editDependsOn" :options="$availableMissions->map(fn($m) => ['value' => $m->mission_title, 'label' => $m->mission_title])->toArray()" placeholder="Nenhuma" :initial="$editDependsOn" />
                </div>

                {{-- Items --}}
                <div class="border border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-semibold text-gray-300">Itens / Critérios</label>
                        <button type="button" wire:click="addItem"
                            class="text-xs bg-green-600 hover:bg-green-500 text-white px-2 py-1 rounded transition">
                            + Item
                        </button>
                    </div>

                    @foreach ($editItems as $itemIndex => $item)
                        <div class="border border-gray-600 rounded p-3 mb-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-400">Item {{ $itemIndex + 1 }}</span>
                                <button type="button" wire:click="removeItem({{ $itemIndex }})"
                                    class="text-xs text-red-400 hover:text-red-300">
                                    <i class="fas fa-times"></i> Remover
                                </button>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="checkbox" wire:model.live="editItems.{{ $itemIndex }}.is_bonus"
                                    value="1" class="form-checkbox h-4 w-4 accent-amber-500 rounded">
                                <label class="text-xs text-gray-400">É bônus?</label>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400">Nome</label>
                                <input type="text" wire:model.defer="editItems.{{ $itemIndex }}.name"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm" placeholder="Nome do item">
                            </div>

                            @if (!($item['is_bonus'] ?? false))
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" wire:model.defer="editItems.{{ $itemIndex }}.has_max"
                                        value="1" class="form-checkbox h-4 w-4 accent-blue-500 rounded">
                                    <label class="text-xs text-gray-400">Tem limite de quantidade?</label>
                                </div>

                                @if ($item['has_max'] ?? false)
                                    <div>
                                        <label class="block text-xs text-gray-400">Quantidade máxima</label>
                                        <input type="number" wire:model.defer="editItems.{{ $itemIndex }}.max_value"
                                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm" min="0">
                                    </div>
                                @endif

                                <div class="border-t border-gray-700 pt-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-400">Opções</span>
                                        <button type="button" wire:click="addItemOption({{ $itemIndex }})"
                                            class="text-xs text-blue-400 hover:text-blue-300">
                                            + Opção
                                        </button>
                                    </div>
                                    @foreach ($item['options'] ?? [] as $optIndex => $option)
                                        <div class="border border-gray-700 rounded p-2 mb-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-2xs text-gray-500">Opção {{ $optIndex + 1 }}</span>
                                                <button type="button" wire:click="removeItemOption({{ $itemIndex }}, {{ $optIndex }})"
                                                    class="text-xs text-red-400 hover:text-red-300">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="mb-1">
                                                <label class="block text-2xs text-gray-500 mb-0.5">Nome</label>
                                                <input type="text" wire:model.defer="editItems.{{ $itemIndex }}.options.{{ $optIndex }}.name"
                                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-xs" placeholder="Ex: Totalmente dentro">
                                            </div>
                                            <div>
                                                <label class="block text-2xs text-gray-500 mb-0.5">Valor</label>
                                                <input type="number" wire:model.defer="editItems.{{ $itemIndex }}.options.{{ $optIndex }}.value"
                                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-xs" placeholder="20">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-amber-900/20 border border-amber-800/50 rounded p-3 space-y-2">
                                    <div>
                                        <label class="block text-xs text-amber-300 font-semibold">Nome do bônus</label>
                                        <span class="text-xs text-gray-400 block mb-1">Use o campo "Nome" acima para definir o nome do bônus.</span>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-amber-300 font-semibold">Valor do bônus (pts)</label>
                                        <input type="number" wire:model.defer="editItems.{{ $itemIndex }}.value"
                                            class="w-full bg-gray-800 border border-amber-700/50 text-amber-200 rounded px-2 py-1 text-sm" placeholder="Ex: 10">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeCreateModal"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition">Salvar</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Edit Modal --}}
    @if ($showEditModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <form wire:submit.prevent="update" class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-lg space-y-4 overflow-y-auto max-h-[85vh]">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Editar Missão DP</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Ano</label>
                    <input type="number" wire:model.defer="editYear" min="2020" max="2099"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                    @error('editYear') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Nível DP</label>
                    <select wire:model.defer="editDpLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="baby">Baby</option>
                        <option value="kids1">Kids 1</option>
                        <option value="kids2">Kids 2</option>
                        <option value="middle">Middle</option>
                        <option value="high">High</option>
                        <option value="technic_university">Technic / University</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Título da Missão</label>
                    <input type="text" wire:model.defer="editMissionTitle"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Descrição</label>
                    <input type="text" wire:model.defer="editDescription"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Depende de (opcional)</label>
                    <x-searchable-select model="editDependsOn" :options="$availableMissions->map(fn($m) => ['value' => $m->mission_title, 'label' => $m->mission_title])->toArray()" placeholder="Nenhuma" :initial="$editDependsOn" />
                </div>

                {{-- Items --}}
                <div class="border border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-semibold text-gray-300">Itens / Critérios</label>
                        <button type="button" wire:click="addItem"
                            class="text-xs bg-green-600 hover:bg-green-500 text-white px-2 py-1 rounded transition">
                            + Item
                        </button>
                    </div>

                    @foreach ($editItems as $itemIndex => $item)
                        <div class="border border-gray-600 rounded p-3 mb-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-400">Item {{ $itemIndex + 1 }}</span>
                                <button type="button" wire:click="removeItem({{ $itemIndex }})"
                                    class="text-xs text-red-400 hover:text-red-300">
                                    <i class="fas fa-times"></i> Remover
                                </button>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="checkbox" wire:model.live="editItems.{{ $itemIndex }}.is_bonus"
                                    value="1" class="form-checkbox h-4 w-4 accent-amber-500 rounded">
                                <label class="text-xs text-gray-400">É bônus?</label>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400">Nome</label>
                                <input type="text" wire:model.defer="editItems.{{ $itemIndex }}.name"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm" placeholder="Nome do item">
                            </div>

                            @if (!($item['is_bonus'] ?? false))
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" wire:model.defer="editItems.{{ $itemIndex }}.has_max"
                                        value="1" class="form-checkbox h-4 w-4 accent-blue-500 rounded">
                                    <label class="text-xs text-gray-400">Tem limite de quantidade?</label>
                                </div>

                                @if ($item['has_max'] ?? false)
                                    <div>
                                        <label class="block text-xs text-gray-400">Quantidade máxima</label>
                                        <input type="number" wire:model.defer="editItems.{{ $itemIndex }}.max_value"
                                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm" min="0">
                                    </div>
                                @endif

                                <div class="border-t border-gray-700 pt-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-400">Opções</span>
                                        <button type="button" wire:click="addItemOption({{ $itemIndex }})"
                                            class="text-xs text-blue-400 hover:text-blue-300">
                                            + Opção
                                        </button>
                                    </div>
                                    @foreach ($item['options'] ?? [] as $optIndex => $option)
                                        <div class="border border-gray-700 rounded p-2 mb-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-2xs text-gray-500">Opção {{ $optIndex + 1 }}</span>
                                                <button type="button" wire:click="removeItemOption({{ $itemIndex }}, {{ $optIndex }})"
                                                    class="text-xs text-red-400 hover:text-red-300">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="mb-1">
                                                <label class="block text-2xs text-gray-500 mb-0.5">Nome</label>
                                                <input type="text" wire:model.defer="editItems.{{ $itemIndex }}.options.{{ $optIndex }}.name"
                                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-xs" placeholder="Ex: Totalmente dentro">
                                            </div>
                                            <div>
                                                <label class="block text-2xs text-gray-500 mb-0.5">Valor</label>
                                                <input type="number" wire:model.defer="editItems.{{ $itemIndex }}.options.{{ $optIndex }}.value"
                                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-xs" placeholder="20">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-amber-900/20 border border-amber-800/50 rounded p-3 space-y-2">
                                    <div>
                                        <label class="block text-xs text-amber-300 font-semibold">Nome do bônus</label>
                                        <span class="text-xs text-gray-400 block mb-1">Use o campo "Nome" acima para definir o nome do bônus.</span>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-amber-300 font-semibold">Valor do bônus (pts)</label>
                                        <input type="number" wire:model.defer="editItems.{{ $itemIndex }}.value"
                                            class="w-full bg-gray-800 border border-amber-700/50 text-amber-200 rounded px-2 py-1 text-sm" placeholder="Ex: 10">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeEditModal"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition">Atualizar</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if ($showDeleteModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 w-full max-w-md ">
                <h2 class="text-xl font-bold text-red-400 mb-4">Confirmar Exclusão</h2>
                <p class="text-gray-300 mb-6">Remover missão <strong>{{ $deleteTarget->mission_title }}</strong>?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="closeDeleteModal"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button wire:click="delete"
                        class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg transition">Excluir</button>
                </div>
            </div>
        </div>
    @endif
</div>

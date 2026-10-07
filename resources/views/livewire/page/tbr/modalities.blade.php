<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-100">Modalidades</h1>
        @can('create')
        <button wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Modalidade
        </button>
        @endcan
    </div>

    <div class="flex gap-4 mb-4">
        <select wire:model.live="filterLevel"
            class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todos os níveis</option>
            <option value="basic">Basic</option>
            <option value="intermediary">Intermediary</option>
            <option value="advanced">Advanced</option>
        </select>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 ">Level</th>
                    <th class="px-4 py-3 ">Slug</th>
                    <th class="px-4 py-3 ">Label</th>
                    <th class="px-4 py-3 ">Config ID</th>
                    <th class="px-4 py-3 text-right w-24">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($modalities as $m)
                    <tr class="hover:bg-gray-800/50 transition">
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $m->level === 'advanced' ? 'bg-purple-900/50 text-purple-300' : ($m->level === 'intermediary' ? 'bg-yellow-900/50 text-yellow-300' : 'bg-blue-900/50 text-blue-300') }}">
                                {{ $m->level }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-100">{{ strtoupper($m->slug) }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $m->label }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs font-mono">{{ $m->config_id }}</td>
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
                        <td colspan="5" class="px-4 py-8  text-gray-500">Nenhuma modalidade cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create Modal --}}
    @if ($showCreateModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <form wire:submit.prevent="save" class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md space-y-4">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Nova Modalidade</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Level</label>
                    <select wire:model.defer="editLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="basic">Basic</option>
                        <option value="intermediary">Intermediary</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Slug</label>
                    <input type="text" wire:model.defer="editSlug"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                    @error('editSlug') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Label</label>
                    <input type="text" wire:model.defer="editLabel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                    @error('editLabel') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Config ID (12 caracteres)</label>
                    <input type="text" wire:model.defer="editConfigId" maxlength="12" 
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 font-mono text-xs">
                    @error('editConfigId') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
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
            <form wire:submit.prevent="update" class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md space-y-4">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Editar Modalidade</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Level</label>
                    <select wire:model.defer="editLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="basic">Basic</option>
                        <option value="intermediary">Intermediary</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Slug</label>
                    <input type="text" wire:model.defer="editSlug"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Label</label>
                    <input type="text" wire:model.defer="editLabel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Config ID</label>
                    <input type="text" wire:model.defer="editConfigId" maxlength="12"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 font-mono text-xs">
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
                <p class="text-gray-300 mb-6">Remover modalidade <strong>{{ $deleteTarget->label }}</strong>?</p>
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

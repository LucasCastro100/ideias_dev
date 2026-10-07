<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-100">Categorias</h1>
        @can('create')
        <button wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Categoria
        </button>
        @endcan
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3  w-10">#</th>
                    <th class="px-4 py-3 ">Label</th>
                    <th class="px-4 py-3 ">Slug</th>
                    <th class="px-4 py-3 ">Modality Level</th>
                    <th class="px-4 py-3 ">Question Level</th>
                    <th class="px-4 py-3 ">DP Level</th>
                    <th class="px-4 py-3 text-right w-40">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($categories as $cat)
                    <tr class="hover:bg-gray-800/50 transition">
                        <td class="px-4 py-3 text-gray-400">
                            <div class="flex items-center gap-1">
                                <button wire:click="moveUp('{{ $cat->id }}')" class="text-gray-500 hover:text-gray-200" title="Subir">
                                    <i class="fas fa-chevron-up text-xs"></i>
                                </button>
                                <span>{{ $loop->iteration }}</span>
                                <button wire:click="moveDown('{{ $cat->id }}')" class="text-gray-500 hover:text-gray-200" title="Descer">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-100">{{ $cat->label }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $cat->slug }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $cat->modality_level }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $cat->question_level }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $cat->dp_level }}</td>
                        <td class="px-4 py-3 text-right">
                            @can('edit')
                            <button wire:click="openEditModal('{{ $cat->id }}')"
                                class="text-blue-400 hover:text-blue-300 mr-3" title="Editar">
                                <i class="fas fa-pen"></i>
                            </button>
                            @endcan
                            @can('delete')
                            <button wire:click="confirmDelete('{{ $cat->id }}')"
                                class="text-red-400 hover:text-red-300" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8  text-gray-500">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create Modal --}}
    @if ($showCreateModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <form wire:submit.prevent="save" class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md space-y-4">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Nova Categoria</h2>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Label</label>
                    <input type="text" wire:model.defer="editLabel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @error('editLabel') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Slug</label>
                    <input type="text" wire:model.defer="editSlug"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @error('editSlug') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Modality Level</label>
                    <select wire:model.defer="editModalityLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="basic">Basic</option>
                        <option value="intermediary">Intermediary</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Question Level</label>
                    <select wire:model.defer="editQuestionLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="basic">Basic</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">DP Level</label>
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
                <h2 class="text-xl font-bold text-gray-100 mb-4">Editar Categoria</h2>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Label</label>
                    <input type="text" wire:model.defer="editLabel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @error('editLabel') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Slug</label>
                    <input type="text" wire:model.defer="editSlug"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @error('editSlug') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Modality Level</label>
                    <select wire:model.defer="editModalityLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="basic">Basic</option>
                        <option value="intermediary">Intermediary</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Question Level</label>
                    <select wire:model.defer="editQuestionLevel"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                        <option value="basic">Basic</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">DP Level</label>
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

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeEditModal"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition">Atualizar</button>
                </div>
            </form>
        </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />
</div>

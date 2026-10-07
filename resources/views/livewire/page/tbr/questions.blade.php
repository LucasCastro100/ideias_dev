<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-100">Perguntas de Avaliação</h1>
        @can('create')
        <button wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Pergunta
        </button>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="flex gap-4 mb-4">
        <select wire:model.live="filterLevel"
            class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todos os níveis</option>
            <option value="basic">Basic</option>
            <option value="advanced">Advanced</option>
        </select>
        <select wire:model.live="filterModality"
            class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todas as modalidades</option>
            <option value="ap">Apresentação (AP)</option>
            <option value="mc">Mérito Científico (MC)</option>
            <option value="om">Organização e Método (OM)</option>
            <option value="te">Tecnologia e Engenharia (TE)</option>
        </select>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 ">Level</th>
                    <th class="px-4 py-3 ">Modalidade</th>
                    <th class="px-4 py-3 ">Objeto</th>
                    <th class="px-4 py-3 ">Critérios</th>
                    <th class="px-4 py-3 text-right w-24">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($questions as $q)
                    <tr class="hover:bg-gray-800/50 transition">
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $q->level === 'advanced' ? 'bg-purple-900/50 text-purple-300' : 'bg-blue-900/50 text-blue-300' }}">
                                {{ $q->level }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ strtoupper($q->modality_slug) }}</td>
                        <td class="px-4 py-3 font-medium text-gray-100">{{ $q->object_name }}</td>
                        <td class="px-4 py-3 text-gray-400">
                            <ul class="list-disc list-inside text-xs space-y-0.5">
                                @foreach ($q->criteria as $c)
                                    <li>{{ Str::limit($c, 60) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('edit')
                            <button wire:click="openEditModal('{{ $q->id }}')"
                                class="text-blue-400 hover:text-blue-300 mr-3" title="Editar">
                                <i class="fas fa-pen"></i>
                            </button>
                            @endcan
                            @can('delete')
                            <button wire:click="openDeleteModal('{{ $q->id }}')"
                                class="text-red-400 hover:text-red-300" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8  text-gray-500">Nenhuma pergunta cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create Modal --}}
    @if ($showCreateModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <form wire:submit.prevent="save"
                class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto space-y-4">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Nova Pergunta</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">Level</label>
                        <select wire:model.defer="editLevel"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                            <option value="basic">Basic</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">Modalidade</label>
                        <x-searchable-select model="editModalitySlug" :options="[['value' => 'ap', 'label' => 'AP - Apresentação'], ['value' => 'mc', 'label' => 'MC - Mérito Científico'], ['value' => 'om', 'label' => 'OM - Organização e Método'], ['value' => 'te', 'label' => 'TE - Tecnologia e Engenharia']]" placeholder="Selecione a modalidade" :initial="$editModalitySlug" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Nome do Objeto</label>
                    <input type="text" wire:model.defer="editObjectName"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @error('editObjectName') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                        <input type="checkbox" wire:model.defer="editMission" class="form-checkbox text-blue-500 bg-gray-800 border-gray-600 rounded">
                        É missão?
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Critérios</label>
                    @foreach ($editCriteria as $index => $criterion)
                        <div class="flex items-center gap-2 mb-2">
                            <textarea rows="2" wire:model.defer="editCriteria.{{ $index }}"
                                class="flex-1 bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                            @if (count($editCriteria) > 1)
                                <button type="button" wire:click="removeCriterion({{ $index }})"
                                    class="text-red-400 hover:text-red-300">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                    <button type="button" wire:click="addCriterion"
                        class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                        <i class="fas fa-plus"></i> Adicionar critério
                    </button>
                    @error('editCriteria') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
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
            <form wire:submit.prevent="update"
                class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto space-y-4">
                <h2 class="text-xl font-bold text-gray-100 mb-4">Editar Pergunta</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">Level</label>
                        <select wire:model.defer="editLevel"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2">
                            <option value="basic">Basic</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">Modalidade</label>
                        <x-searchable-select model="editModalitySlug" :options="[['value' => 'ap', 'label' => 'AP - Apresentação'], ['value' => 'mc', 'label' => 'MC - Mérito Científico'], ['value' => 'om', 'label' => 'OM - Organização e Método'], ['value' => 'te', 'label' => 'TE - Tecnologia e Engenharia']]" placeholder="Selecione a modalidade" :initial="$editModalitySlug" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">Nome do Objeto</label>
                    <input type="text" wire:model.defer="editObjectName"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @error('editObjectName') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                        <input type="checkbox" wire:model.defer="editMission" class="form-checkbox text-blue-500 bg-gray-800 border-gray-600 rounded">
                        É missão?
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Critérios</label>
                    @foreach ($editCriteria as $index => $criterion)
                        <div class="flex items-center gap-2 mb-2">
                            <textarea rows="2" wire:model.defer="editCriteria.{{ $index }}"
                                class="flex-1 bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                            @if (count($editCriteria) > 1)
                                <button type="button" wire:click="removeCriterion({{ $index }})"
                                    class="text-red-400 hover:text-red-300">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                    <button type="button" wire:click="addCriterion"
                        class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                        <i class="fas fa-plus"></i> Adicionar critério
                    </button>
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
                <p class="text-gray-300 mb-6">Remover a pergunta <strong>{{ $deleteTarget->object_name }}</strong>?</p>
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

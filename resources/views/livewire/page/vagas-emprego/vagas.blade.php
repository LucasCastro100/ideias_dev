<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Vagas</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Nova Vaga">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Nova Vaga</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Buscar por título, categoria ou cidade..." class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
            </div>
            <div class="flex items-center gap-1.5 ml-auto">
                <span class="text-xs text-gray-500">Qtd</span>
                <select wire:model.live="perPage" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1.5 text-sm w-16">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-400">
            <thead class="text-xs uppercase bg-gray-800">
                <tr>
                    <th class="px-6 py-3">Título</th>
                    <th class="px-6 py-3">Empresa</th>
                    <th class="px-6 py-3">Categoria</th>
                    <th class="px-6 py-3">Salário</th>
                    <th class="px-6 py-3">Cidade/UF</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($jobs as $j)
                    <tr class="hover:bg-gray-800/50">
                        <td class="px-6 py-4 text-white">{{ $j->title }}</td>
                        <td class="px-6 py-4">{{ $j->company?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $j->category ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $j->salary ? 'R$ ' . number_format($j->salary, 2, ',', '.') : '-' }}</td>
                        <td class="px-6 py-4">{{ trim(($j->city ?? '') . ($j->state ? '/' . $j->state : '')) ?: '-' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'aberta' => 'bg-green-900 text-green-400',
                                    'pausada' => 'bg-yellow-900 text-yellow-400',
                                    'encerrada' => 'bg-gray-800 text-gray-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$j->status] ?? 'bg-gray-800 text-gray-400' }}">
                                {{ ucfirst($j->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $j->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $j->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-gray-500">Nenhuma vaga encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $jobs->firstItem() ?? 0 }} a {{ $jobs->lastItem() ?? 0 }} de {{ $jobs->total() }} vagas
        </div>
    </div>

    @if ($jobs->hasPages())
    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $jobId ? 'Editar' : 'Nova' }} Vaga</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Título</label>
                        <input wire:model="title" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('title') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Empresa</label>
                        <x-searchable-select model="company_id" :options="$companies->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" placeholder="Selecione a empresa" :initial="$company_id" />
                        @error('company_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Categoria</label>
                            <input wire:model="category" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('category') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Salário (R$)</label>
                            <input wire:model="salary" type="number" step="0.01" min="0" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('salary') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <x-location-select :regions="$regions" :filtered-states="$filteredStates" :filtered-cities="$filteredCities" />
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Expira em</label>
                            <input wire:model="expires_at" type="date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('expires_at') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Status</label>
                            <select wire:model="status" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="aberta">Aberta</option>
                                <option value="pausada">Pausada</option>
                                <option value="encerrada">Encerrada</option>
                            </select>
                            @error('status') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <x-rich-text-editor model="description" label="Descrição" />
                    <x-rich-text-editor model="requirements" label="Requisitos" />
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />
</div>

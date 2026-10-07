<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Processos</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Processo">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Processo</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
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
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Nº Processo</th>
                    <th class="px-6 py-3">Tribunal</th>
                    <th class="px-6 py-3">Fase</th>
                    <th class="px-6 py-3">Audiência</th>
                    <th class="px-6 py-3">Honorários</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($processos as $p)
                    <tr class="hover:bg-gray-800/50">
                        <td class="px-6 py-4 text-white">{{ $p->title }}</td>
                        <td class="px-6 py-4">{{ $p->client?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $p->case_no ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $p->court ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $stageColors = ['inicial' => 'bg-gray-700 text-gray-300', 'andamento' => 'bg-blue-900 text-blue-400', 'recurso' => 'bg-yellow-900 text-yellow-400', 'encerrado' => 'bg-green-900 text-green-400'];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $stageColors[$p->stage] ?? 'bg-gray-700 text-gray-300' }}">
                                {{ ucfirst($p->stage) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $p->hearing_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $p->fees !== null ? 'R$ ' . number_format($p->fees, 2, ',', '.') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $p->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $p->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-gray-500">Nenhum processo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $processos->firstItem() ?? 0 }} a {{ $processos->lastItem() ?? 0 }} de {{ $processos->total() }} processos
        </div>
    </div>

    @if ($processos->hasPages())
        <div class="mt-4">
            {{ $processos->links() }}
        </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $processoId ? 'Editar' : 'Novo' }} Processo</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Cliente</label>
                        <x-searchable-select model="client_id" :options="$clientes->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" placeholder="Selecione o cliente" :initial="$client_id" />
                        @error('client_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Título</label>
                        <input wire:model="title" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('title') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Nº Processo</label>
                            <input wire:model="case_no" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('case_no') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Tribunal</label>
                            <input wire:model="court" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('court') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Fase</label>
                            <select wire:model="stage" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="inicial">Inicial</option>
                                <option value="andamento">Andamento</option>
                                <option value="recurso">Recurso</option>
                                <option value="encerrado">Encerrado</option>
                            </select>
                            @error('stage') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Data da Audiência</label>
                            <input wire:model="hearing_date" type="date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('hearing_date') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Honorários (R$)</label>
                        <input wire:model="fees" type="number" step="0.01" min="0" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('fees') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
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

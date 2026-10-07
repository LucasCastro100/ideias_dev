<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Anúncios</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Anúncio">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Anúncio</span>
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
                    <th class="px-6 py-3">Tipo</th>
                    <th class="px-6 py-3">Preço</th>
                    <th class="px-6 py-3">Lance Atual</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Encerra em</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($anuncios as $a)
                    <tr class="hover:bg-gray-800/50">
                        <td class="px-6 py-4 text-white">{{ $a->title }}</td>
                        <td class="px-6 py-4">{{ $a->listing_type === 'leilao' ? 'Leilão' : 'Preço Fixo' }}</td>
                        <td class="px-6 py-4">{{ $a->price !== null ? 'R$ ' . number_format($a->price, 2, ',', '.') : '-' }}</td>
                        <td class="px-6 py-4">{{ $a->current_bid !== null ? 'R$ ' . number_format($a->current_bid, 2, ',', '.') : '-' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'ativo' => 'bg-green-900 text-green-400',
                                    'encerrado' => 'bg-gray-800 text-gray-400',
                                    'cancelado' => 'bg-red-900 text-red-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$a->status] ?? 'bg-gray-800 text-gray-400' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $a->ends_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $a->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $a->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-gray-500">Nenhum anúncio encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $anuncios->firstItem() ?? 0 }} a {{ $anuncios->lastItem() ?? 0 }} de {{ $anuncios->total() }} anúncios
        </div>
    </div>

    @if ($anuncios->hasPages())
        <div class="mt-4">
            {{ $anuncios->links() }}
        </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $anuncioId ? 'Editar' : 'Novo' }} Anúncio</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Título</label>
                        <input wire:model="title" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('title') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <x-rich-text-editor model="description" label="Descrição" />
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Tipo de Anúncio</label>
                            <select wire:model="listing_type" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="fixo">Preço Fixo</option>
                                <option value="leilao">Leilão</option>
                            </select>
                            @error('listing_type') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Status</label>
                            <select wire:model="status" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="ativo">Ativo</option>
                                <option value="encerrado">Encerrado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                            @error('status') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Preço (R$)</label>
                            <input wire:model="price" type="number" step="0.01" min="0" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('price') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Lance Atual (R$)</label>
                            <input wire:model="current_bid" type="number" step="0.01" min="0" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('current_bid') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Encerra em</label>
                        <input wire:model="ends_at" type="datetime-local" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('ends_at') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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

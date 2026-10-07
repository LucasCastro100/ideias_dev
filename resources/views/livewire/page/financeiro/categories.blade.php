<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Categorias Financeiras</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Nova Categoria">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Nova Categoria</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-end gap-1.5">
            <span class="text-xs text-gray-500">Qtd</span>
            <select wire:model.live="perPage" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1.5 text-sm w-16">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-400 min-w-[600px]">
            <thead class="text-xs uppercase bg-gray-800">
                <tr>
                    <th class="px-6 py-3">Cor</th>
                    <th class="px-6 py-3">Nome</th>
                    <th class="px-6 py-3">Tipo</th>
                    <th class="px-6 py-3">Descrição</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($categories as $cat)
                    <tr class="hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <span class="inline-block w-5 h-5 rounded-full" style="background: {{ $cat->color ?? '#6b7280' }}"></span>
                        </td>
                        <td class="px-6 py-4 text-white">{{ $cat->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if ($cat->type === 'income')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900 text-green-300">Receita</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-900 text-red-300">Despesa</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $cat->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $cat->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $cat->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-gray-500">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $categories->firstItem() ?? 0 }} a {{ $categories->lastItem() ?? 0 }} de {{ $categories->total() }} categorias
        </div>
    </div>

    @if ($categories->hasPages())
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $catId ? 'Editar' : 'Nova' }} Categoria</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Nome</label>
                        <input wire:model="name" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Tipo</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                <input type="radio" wire:model="type" value="income" class="text-green-500 focus:ring-green-500">
                                Receita
                            </label>
                            <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                <input type="radio" wire:model="type" value="expense" class="text-red-500 focus:ring-red-500">
                                Despesa
                            </label>
                        </div>
                        @error('type') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Cor</label>
                        <div class="flex items-center gap-3">
                            <input type="color" wire:model="color" class="w-10 h-10 rounded cursor-pointer bg-transparent border-0">
                            <input wire:model="color" maxlength="9" placeholder="#6b7280" class="flex-1 bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                        </div>
                        @error('color') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Descrição</label>
                        <textarea wire:model="description" rows="3" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('description') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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

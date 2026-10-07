<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Tipos de Conta</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Tipo">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Tipo</span>
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
        <table class="w-full text-sm text-left text-gray-400 min-w-[500px]">
            <thead class="text-xs uppercase bg-gray-800">
                <tr>
                    <th class="px-6 py-3">Nome</th>
                    <th class="px-6 py-3">Descrição</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($types as $type)
                    <tr class="hover:bg-gray-800/50">
                        <td class="px-6 py-4 text-white">{{ $type->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $type->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $type->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $type->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-gray-500">Nenhum tipo de conta cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $types->firstItem() ?? 0 }} a {{ $types->lastItem() ?? 0 }} de {{ $types->total() }} tipos
        </div>
    </div>

    @if ($types->hasPages())
    <div class="mt-4">
        {{ $types->links() }}
    </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $typeId ? 'Editar' : 'Novo' }} Tipo de Conta</h3>
                <form wire:submit="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Nome</label>
                            <input wire:model="name" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Descrição</label>
                            <textarea wire:model="description" rows="3" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />
</div>

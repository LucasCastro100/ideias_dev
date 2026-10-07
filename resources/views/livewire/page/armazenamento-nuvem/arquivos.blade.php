<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Arquivos</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Arquivo">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Arquivo</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nome..."
                class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    <th class="px-6 py-3">Nome</th>
                    <th class="px-6 py-3">Pasta</th>
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Tamanho</th>
                    <th class="px-6 py-3">Downloads</th>
                    <th class="px-6 py-3">Público</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($files as $f)
                    <tr class="hover:bg-gray-800/50">
                        <td class="px-6 py-4 text-white"><i class="fas fa-file text-blue-400 mr-2"></i>{{ $f->name }}</td>
                        <td class="px-6 py-4">{{ $f->folder?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $f->client?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $f->size_kb !== null ? number_format($f->size_kb, 0, ',', '.') . ' KB' : '-' }}</td>
                        <td class="px-6 py-4">{{ $f->downloads_count }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $f->is_public ? 'bg-green-900 text-green-400' : 'bg-gray-800 text-gray-400' }}">
                                {{ $f->is_public ? 'Público' : 'Privado' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $f->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $f->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-gray-500">Nenhum arquivo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $files->firstItem() ?? 0 }} a {{ $files->lastItem() ?? 0 }} de {{ $files->total() }} arquivos
        </div>
    </div>

    @if ($files->hasPages())
    <div class="mt-4">
        {{ $files->links() }}
    </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $fileId ? 'Editar' : 'Novo' }} Arquivo</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Nome</label>
                        <input wire:model="name" type="text" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Pasta</label>
                        <x-searchable-select model="folder_id" :options="$folders->map(fn($f) => ['value' => $f->id, 'label' => $f->name])->toArray()" placeholder="Selecione a pasta" :initial="$folder_id" />
                        @error('folder_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Cliente (opcional, para compartilhamento)</label>
                        <x-searchable-select model="client_id" :options="$clientes->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" placeholder="Nenhum cliente vinculado" :initial="$client_id" />
                        @error('client_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <x-rich-text-editor model="description" label="Descrição" />
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Tamanho (KB)</label>
                        <input wire:model="size_kb" type="number" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('size_kb') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Downloads</label>
                        <input wire:model="downloads_count" type="number" min="0" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('downloads_count') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                            <input type="checkbox" wire:model="is_public" class="rounded bg-gray-800 border-gray-700 text-blue-500 focus:ring-blue-500">
                            Público
                        </label>
                        @error('is_public') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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

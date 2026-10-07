<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-200">Gerenciar Sistemas</h2>
            <button wire:click="openModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Novo Sistema
            </button>
        </div>

        <div class="bg-gray-900 border border-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs uppercase bg-gray-800 text-gray-400">
                        <tr>
                            <th class="px-6 py-3 ">Ativo</th>
                            <th class="px-6 py-3 ">Nome</th>
                            <th class="px-6 py-3 ">Slug</th>
                            <th class="px-6 py-3 ">Descrição</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @foreach ($systems as $system)
                            <tr class="hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $system->active ? 'bg-green-900 text-green-400' : 'bg-gray-700 text-gray-400' }}">
                                        {{ $system->active ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-white">{{ $system->name }}</td>
                                <td class="px-6 py-4">{{ $system->slug }}</td>
                                <td class="px-6 py-4">{{ $system->description }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="edit({{ $system->id }})" class="text-blue-500 hover:text-blue-400">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($showModal)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ $systemId ? 'Editar' : 'Novo' }} Sistema</h3>
                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Nome</label>
                                <input wire:model="name" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Slug</label>
                                <input wire:model="slug" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                @error('slug') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Descrição</label>
                                <textarea wire:model="description" rows="3" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-gray-400">
                                    <input type="checkbox" wire:model="active" class="rounded bg-gray-800 border-gray-700">
                                    Ativo
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded hover:bg-gray-600">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

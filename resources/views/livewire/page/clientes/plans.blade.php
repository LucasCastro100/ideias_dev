<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Planos</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Plano">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Plano</span>
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
                        <th class="px-6 py-3 ">Ativo</th>
                        <th class="px-6 py-3 ">Nome</th>
                        <th class="px-6 py-3 ">Descrição</th>
                        <th class="px-6 py-3 ">Valor</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($plans as $plan)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $plan->active ? 'bg-green-900 text-green-400' : 'bg-gray-700 text-gray-400' }}">
                                    {{ $plan->active ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-white">{{ $plan->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $plan->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-green-400 font-medium">R$ {{ $plan->value ? number_format($plan->value, 2, ',', '.') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="edit({{ $plan->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $plan->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12  text-gray-500">Nenhum plano cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-end mt-4">
            <div class="text-xs text-gray-500">
                Mostrando {{ $plans->firstItem() ?? 0 }} a {{ $plans->lastItem() ?? 0 }} de {{ $plans->total() }} planos
            </div>
        </div>

        @if ($plans->hasPages())
        <div class="mt-4">
            {{ $plans->links() }}
        </div>
        @endif

        <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />

        @if ($showModal)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ $planId ? 'Editar' : 'Novo' }} Plano</h3>
                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Nome</label>
                                <input wire:model="name" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Descrição</label>
                                <textarea wire:model="description" rows="3" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Valor</label>
                                <input x-data="{ display: '' }"
                                    x-init="display = $wire.value ? parseFloat($wire.value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : ''"
                                    x-on:input="
                                        let v = $event.target.value.replace(/\D/g, '');
                                        if (v.length === 0) { display = ''; $wire.set('value', ''); return; }
                                        let n = (parseInt(v) / 100).toFixed(2);
                                        display = n.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                        $wire.set('value', parseFloat(n));
                                    "
                                    x-bind:value="display"
                                    inputmode="numeric" placeholder="0,00"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                @error('value') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
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

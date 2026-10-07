<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Gastos</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Gasto">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Gasto</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Mês</label>
                <select wire:model.live="filterMonth" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Ano</label>
                <select wire:model.live="filterYear" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
                    @foreach (range(now()->year - 5, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
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

    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-x-auto w-full">
        <table class="w-full text-sm text-left text-gray-400 min-w-[600px]">
            <thead class="text-xs uppercase bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 ">Descrição</th>
                        <th class="px-6 py-3 ">Valor</th>
                        <th class="px-6 py-3 ">Mês</th>
                        @if (auth()->user()->isSuperAdmin())<th class="px-6 py-3">Usuário</th>@endif
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($accounts as $acc)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4 text-white">{{ $acc->description ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-red-400">
                            R$ {{ $acc->value ? number_format($acc->value, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4">{{ str_pad($acc->month ?? 0, 2, '0', STR_PAD_LEFT) }}/{{ $acc->year ?? '-' }}</td>
                        @if (auth()->user()->isSuperAdmin())
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $acc->team?->name ?? $acc->user?->name ?? '-' }}</td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $acc->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $acc->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 5 : 4 }}" class="px-6 py-12  text-gray-500">Nenhum gasto encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $accounts->firstItem() ?? 0 }} a {{ $accounts->lastItem() ?? 0 }} de {{ $accounts->total() }} gastos
        </div>
    </div>

    @if ($accounts->hasPages())
    <div class="mt-4">
        {{ $accounts->links() }}
    </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $accountId ? 'Editar' : 'Novo' }} Gasto</h3>
                <form wire:submit="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Descrição</label>
                            <input wire:model="description" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                            @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Valor (R$)</label>
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
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Mês</label>
                                <select wire:model="month" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>
                                @error('month') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Ano</label>
                                <select wire:model="year" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                    @foreach (range(now()->year - 2, now()->year + 2) as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                                @error('year') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="border-t border-gray-700 pt-4">
                            <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                <input type="checkbox" wire:model.live="recurring" class="rounded bg-gray-800 border-gray-700 text-blue-500 focus:ring-blue-500">
                                Recorrente (gerar até o fim do período)
                            </label>
                        </div>
                        @if ($recurring)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Até o mês</label>
                                    <select wire:model="recurring_until_month" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                        @foreach (range(1, 12) as $m)
                                            <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Até o ano</label>
                                    <select wire:model="recurring_until_year" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
                                        @foreach (range(now()->year, now()->year + 5) as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Observações</label>
                            <textarea wire:model="notes" rows="2" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2"></textarea>
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
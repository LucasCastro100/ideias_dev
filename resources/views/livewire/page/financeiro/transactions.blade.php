<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Contas Financeiras</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Nova Conta">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Nova Conta</span>
            </button>
        </div>
    </div>

        <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-4 flex-wrap">
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

        <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs uppercase bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 ">Descrição</th>
                        <th class="px-6 py-3 ">Tipo</th>
                        <th class="px-6 py-3 ">Categoria</th>
                        <th class="px-6 py-3 ">Valor</th>
                        <th class="px-6 py-3 ">Vencimento</th>
                        <th class="px-6 py-3 ">Status</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($transactions as $t)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4 text-white">{{ $t->description ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $t->accountType?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($t->category)
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background: {{ $t->category->color ?? '#6b7280' }}"></span>
                                        {{ $t->category->name }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium {{ isset($t->value) && $t->value >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                R$ {{ isset($t->value) ? number_format($t->value, 2, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4">{{ $t->due_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $isOverdue = !$t->paid && $t->due_date && $t->due_date->isPast();
                                @endphp
                                <button wire:click="togglePaid({{ $t->id }})"
                                    class="px-2 py-1 text-xs rounded-full {{ $t->paid ? 'bg-green-900 text-green-400' : ($isOverdue ? 'bg-red-900 text-red-400' : 'bg-yellow-900 text-yellow-400') }}">
                                    {{ $t->paid ? 'Pago' : ($isOverdue ? 'Atrasado' : 'Pendente') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="edit({{ $t->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $t->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12  text-gray-500">Nenhuma conta encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $transactions->firstItem() ?? 0 }} a {{ $transactions->lastItem() ?? 0 }} de {{ $transactions->total() }} contas
        </div>
    </div>

    @if ($transactions->hasPages())
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
    @endif

        @if ($showModal)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ $transactionId ? 'Editar' : 'Nova' }} Conta</h3>
                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Tipo</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                    <input type="radio" wire:model="type" value="expense" class="text-red-500 focus:ring-red-500">
                                    <span class="text-red-400">Despesa</span>
                                </label>
                                <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                    <input type="radio" wire:model="type" value="income" class="text-green-500 focus:ring-green-500">
                                    <span class="text-green-400">Receita</span>
                                </label>
                            </div>
                            @error('type') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Valor (R$)</label>
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
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('value') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Tipo de Conta</label>
                                <x-searchable-select model="account_type_id" :options="$types->map(fn($t) => ['value' => $t->id, 'label' => $t->name])->toArray()" placeholder="Selecione o tipo" :initial="$account_type_id" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Categoria</label>
                                <x-searchable-select model="category_id" :options="$cats->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" placeholder="Selecione a categoria" :initial="$category_id" />
                                @error('category_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Vencimento</label>
                                <input wire:model="due_date" type="date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('due_date') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                <input type="checkbox" wire:model="paid" class="rounded bg-gray-800 border-gray-700 text-blue-500 focus:ring-blue-500">
                                Pago
                            </label>
                        </div>
                        @if ($paid)
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Data de Pagamento</label>
                                <input wire:model="paid_date" type="date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Observações</label>
                            <textarea wire:model="notes" rows="2" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('notes') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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

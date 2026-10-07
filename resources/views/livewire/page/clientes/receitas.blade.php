<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Receitas</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Nova Receita">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Nova Receita</span>
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
                        <th class="px-6 py-3 ">Cliente</th>
                        <th class="px-6 py-3 ">Valor</th>
                        <th class="px-6 py-3 ">Mês</th>
                        <th class="px-6 py-3 text-center">Pago</th>
                        @if (auth()->user()->isSuperAdmin())<th class="px-6 py-3">Usuário</th>@endif
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($receitas as $r)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4 text-white">{{ $r->client?->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-green-400">
                            R$ {{ $r->value ? number_format($r->value, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4">{{ str_pad($r->month ?? 0, 2, '0', STR_PAD_LEFT) }}/{{ $r->year ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $isOverdue = !$r->paid && $r->due_date && $r->due_date->isPast();
                            @endphp
                            @if ($r->paid)
                                <i class="fas fa-check-circle text-green-400" title="Pago"></i>
                            @elseif ($isOverdue)
                                <i class="fas fa-exclamation-circle text-red-400" title="Atrasado"></i>
                            @else
                                <i class="fas fa-clock text-yellow-500" title="Pendente"></i>
                            @endif
                        </td>
                        @if (auth()->user()->isSuperAdmin())
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $r->team?->name ?? $r->user?->name ?? '-' }}</td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openHistory({{ $r->client_id }})" class="p-1.5 text-purple-500 hover:text-purple-400 hover:bg-purple-500/10 rounded-lg transition" title="Histórico do Cliente">
                                    <i class="fas fa-history"></i>
                                </button>
                                <button wire:click="edit({{ $r->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $r->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isSuperAdmin() ? 6 : 5 }}" class="px-6 py-12  text-gray-500">Nenhuma receita cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $receitas->firstItem() ?? 0 }} a {{ $receitas->lastItem() ?? 0 }} de {{ $receitas->total() }} receitas
        </div>
    </div>

    @if ($receitas->hasPages())
    <div class="mt-4">
        {{ $receitas->links() }}
    </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $receitaId ? 'Editar' : 'Nova' }} Receita</h3>
                <form wire:submit="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Cliente</label>
                            <x-searchable-select model="client_id" :options="$clients->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" placeholder="Selecione um cliente" :initial="$client_id" />
                            @error('client_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Plano</label>
                            <x-searchable-select model="plan_id" :options="$plans->map(fn($p) => ['value' => $p->id, 'label' => $p->name . ' — R$ ' . number_format($p->value, 2, ',', '.')])->toArray()" placeholder="Selecione um plano" :initial="$plan_id" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Valor (R$)</label>
                            <div x-data="{ display: '' }"
                                x-init="$watch('$wire.value', v => { display = v ? parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0,00'; })"
                                class="w-full bg-gray-800/50 border border-gray-700 text-gray-400 rounded px-3 py-2"
                                x-text="display || '0,00'">
                            </div>
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
                        <div class="border-t border-gray-700 pt-4">
                            <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                <input type="checkbox" wire:model.live="paid" class="rounded bg-gray-800 border-gray-700 text-green-500 focus:ring-green-500">
                                Pago
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Data de Vencimento</label>
                            <input wire:model="due_date" type="date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2">
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

    @if ($showHistoryModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Histórico — {{ $historyClientName }}</h3>
                    <button wire:click="closeHistory" class="text-gray-500 hover:text-gray-300 text-xl leading-none">&times;</button>
                </div>

                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-300 uppercase tracking-wide mb-3">Aulas</h4>
                    @if (count($historyLogs))
                        <div class="space-y-2">
                            @foreach ($historyLogs as $log)
                                <div class="flex items-start gap-3 bg-gray-800/50 rounded-lg p-3">
                                    <i class="fas fa-chalkboard text-blue-400 mt-1"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white">{!! $log['description'] !!}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($log['lesson_date'])->format('d/m/Y') }} — {{ $log['user']['name'] ?? '-' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Nenhuma aula registrada.</p>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-300 uppercase tracking-wide mb-3">Recebimentos</h4>
                    @if (count($historyAccounts))
                        <div class="space-y-2">
                            @foreach ($historyAccounts as $acc)
                                <div class="flex items-start gap-3 bg-gray-800/50 rounded-lg p-3">
                                    @php
                                        $isOverdue = !$acc['paid'] && $acc['due_date'] && \Carbon\Carbon::parse($acc['due_date'])->isPast();
                                    @endphp
                                    <i class="fas {{ $acc['paid'] ? 'fa-check-circle text-green-400' : ($isOverdue ? 'fa-exclamation-circle text-red-400' : 'fa-clock text-yellow-500') }} mt-1"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white">R$ {{ number_format($acc['value'], 2, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ str_pad($acc['month'], 2, '0', STR_PAD_LEFT) }}/{{ $acc['year'] }} — {{ $acc['paid'] ? 'Pago' : ($isOverdue ? 'Atrasado' : 'Pendente') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Nenhum recebimento registrado.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
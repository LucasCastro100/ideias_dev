<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Financeiro</h1>
    </div>

    {{-- Cards + Chart lado a lado --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-1 flex flex-col gap-2">
            <x-dashboard-card label="Total Receitas" color="text-green-400" value="R$ {{ number_format($totalReceitas, 2, ',', '.') }}" />
            <x-dashboard-card label="Total Despesas" color="text-red-400" value="R$ {{ number_format(abs($totalDespesas), 2, ',', '.') }}" />
            <x-dashboard-card label="Saldo" color="{{ $saldo >= 0 ? 'text-blue-400' : 'text-red-400' }}" value="R$ {{ number_format($saldo, 2, ',', '.') }}" />
            <x-dashboard-card label="Pendentes" color="text-yellow-400" :value="$pendingCount" />
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-2.5">
                <p class="text-sm text-gray-200 uppercase tracking-wide">Receitas do mês</p>
                <p class="text-xl font-bold text-blue-400">R$ {{ number_format($currentMonthReceitas, 2, ',', '.') }}</p>
                <p class="text-xs {{ $receitaChangePercent >= 0 ? 'text-green-500' : 'text-red-500' }}">{{ $receitaChangePercent >= 0 ? '+' : '' }}{{ $receitaChangePercent }}%</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-2.5">
                <p class="text-sm text-gray-200 uppercase tracking-wide">Despesas do mês</p>
                <p class="text-xl font-bold text-red-400">R$ {{ number_format(abs($currentMonthDespesas), 2, ',', '.') }}</p>
                <p class="text-xs {{ $despesaChangePercent <= 0 ? 'text-green-500' : 'text-red-500' }}">{{ $despesaChangePercent >= 0 ? '+' : '' }}{{ $despesaChangePercent }}%</p>
            </div>
        </div>
        {{-- Gráfico: Tendência Mensal --}}
        <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-lg p-5 flex flex-col">
            <h3 class="text-sm font-semibold text-gray-300 mb-3 uppercase tracking-wide">Tendência Mensal</h3>
            <div class="flex-1 min-h-[220px]">
                <canvas id="trendChart" x-data="{}" x-init="
                    new Chart($el, {
                        type: 'line',
                        data: {
                            labels: {{ $monthlyTrendJson }}.map(d => d.month),
                            datasets: [
                                {
                                    label: 'Receitas',
                                    data: {{ $monthlyTrendJson }}.map(d => d.receitas),
                                    borderColor: '#34d399',
                                    backgroundColor: 'rgba(52, 211, 153, 0.1)',
                                    fill: true, tension: 0.3,
                                    pointRadius: 3,
                                },
                                {
                                    label: 'Despesas',
                                    data: {{ $monthlyTrendJson }}.map(d => d.despesas),
                                    borderColor: '#f87171',
                                    backgroundColor: 'rgba(248, 113, 113, 0.1)',
                                    fill: true, tension: 0.3,
                                    pointRadius: 3,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { color: '#9ca3af', boxWidth: 12, padding: 8 } }
                            },
                            scales: {
                                x: { ticks: { color: '#6b7280', font: { size: 10 } }, grid: { color: '#1f2937' } },
                                y: { ticks: { color: '#6b7280', font: { size: 10 }, callback: v => 'R$ ' + v.toFixed(0) }, grid: { color: '#1f2937' } }
                            }
                        }
                    })
                "></canvas>
            </div>
        </div>
    </div>

    {{-- Gráficos extras + Contas Recentes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
            <h3 class="text-sm font-semibold text-gray-300 mb-3 uppercase tracking-wide">Maiores Despesas por Tipo de Conta</h3>
            @if (count($topAccountTypes) > 0)
                <div class="h-52">
                    <canvas id="accountTypeChart" x-data="{}" x-init="
                        new Chart($el, {
                            type: 'bar',
                            data: {
                                labels: {{ $topAccountTypesJson }}.map(d => d.name),
                                datasets: [{
                                    label: 'Total',
                                    data: {{ $topAccountTypesJson }}.map(d => d.value),
                                    backgroundColor: ['#f87171', '#fb923c', '#fbbf24', '#a78bfa', '#60a5fa'],
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { ticks: { color: '#6b7280', font: { size: 10 }, callback: v => 'R$ ' + v.toFixed(0) }, grid: { color: '#1f2937' } },
                                    y: { ticks: { color: '#9ca3af', font: { size: 10 } }, grid: { display: false } }
                                }
                            }
                        })
                    "></canvas>
                </div>
            @else
                <p class="text-gray-500 text-sm text-center mt-8">Nenhum dado disponível.</p>
            @endif
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wide">Contas Recentes</h3>
                <a href="{{ route('financeiro.transactions') }}" wire:navigate class="text-xs text-blue-500 hover:text-blue-400">Ver todas</a>
            </div>
            <div class="divide-y divide-gray-800">
                @forelse ($recentTransactions as $t)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <p class="text-white font-medium text-sm">{{ $t->description }}</p>
                            <p class="text-xs text-gray-500">{{ $t->month }}/{{ $t->year }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-sm {{ $t->value >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                R$ {{ number_format($t->value, 2, ',', '.') }}
                            </p>
                            <span class="text-xs {{ $t->paid ? 'text-green-500' : ($t->due_date && $t->due_date->isPast() ? 'text-red-500' : 'text-yellow-500') }}">
                                {{ $t->paid ? 'Pago' : ($t->due_date && $t->due_date->isPast() ? 'Atrasado' : 'Pendente') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-gray-500">Nenhuma conta registrada.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

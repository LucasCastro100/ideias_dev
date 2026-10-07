<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Clientes</h1>
    </div>

    {{-- Cards + Chart lado a lado --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-8">
        <div class="lg:col-span-1 flex flex-col gap-2">
            <x-dashboard-card label="Total de Clientes" color="text-cyan-400" :value="$totalClients" span />
            <x-dashboard-card label="Receita Prevista" color="text-cyan-400" value="R$ {{ number_format($expectedRevenue, 2, ',', '.') }}" />
            <x-dashboard-card label="Receita Arrecadada" color="text-blue-400" value="R$ {{ number_format($totalCollected, 2, ',', '.') }}" />
            <x-dashboard-card label="Gasto" color="text-red-400" value="R$ {{ number_format($monthlyExpenses, 2, ',', '.') }}" />
            <x-dashboard-card label="Total Líquido" color="{{ $netTotal >= 0 ? 'text-emerald-400' : 'text-red-400' }}" value="R$ {{ number_format($netTotal, 2, ',', '.') }}" />
        </div>
        {{-- Gráfico mensal --}}
        @php $trendJson = json_encode($monthlyTrend); @endphp
        <div class="lg:col-span-3 bg-gray-900 border border-gray-800 rounded-lg p-5 flex flex-col">
            <h3 class="text-sm font-semibold text-gray-300 mb-3 uppercase tracking-wide">Receitas vs Gastos</h3>
            <div class="flex-1 min-h-[220px]">
                <canvas id="clientesTrendChart"
                        x-data="{ trend: {{ $trendJson }} }"
                        x-init="
                            let t = trend;
                            setTimeout(() => {
                                new Chart($el, {
                                    type: 'line',
                                    data: {
                                        labels: t.map(d => d.month),
                                        datasets: [
                                            {
                                                label: 'Receitas',
                                                data: t.map(d => d.receitas),
                                                borderColor: '#3b82f6',
                                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                                fill: true, tension: 0.3,
                                                pointRadius: 3,
                                            },
                                            {
                                                label: 'Gastos',
                                                data: t.map(d => d.despesas),
                                                borderColor: '#ef4444',
                                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
                                            y: { beginAtZero: true, ticks: { color: '#6b7280', font: { size: 10 }, callback: v => 'R$ ' + v.toFixed(0) }, grid: { color: '#1f2937' } }
                                        }
                                    }
                                })
                            }, 100);
                        "></canvas>
            </div>
        </div>
    </div>

    {{-- Clientes Recentes --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-200 mb-4">Clientes Recentes</h3>
        <div class="bg-gray-900 border border-gray-800 rounded-lg divide-y divide-gray-800">
            @forelse ($recentClients as $client)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="text-white font-medium">{{ $client->name }}</p>
                        <p class="text-xs text-gray-500">{{ $client->email ?? 'Sem email' }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $client->created_at->format('d/m/Y') }}</span>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">Nenhum cliente cadastrado.</div>
            @endforelse
        </div>
    </div>
</div>
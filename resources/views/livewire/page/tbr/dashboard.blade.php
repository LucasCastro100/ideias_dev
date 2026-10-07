<div>
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="bg-green-600 text-white p-3 rounded-lg mb-4 transition duration-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Dashboard TBR</h1>
            <div class="flex gap-2">
                <button @click="window.Livewire.dispatch('openEventModal')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition text-sm font-medium">
                    <i class="fas fa-calendar-plus mr-1"></i> Novo Evento
                </button>
                <button @click="window.Livewire.dispatch('openTeamModal')"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition text-sm font-medium">
                    <i class="fas fa-users mr-1"></i> Nova Equipe
                </button>
            </div>
        </div>
    </div>

    {{-- Cards + Chart lado a lado --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-1 flex flex-col gap-2">
            <x-dashboard-card label="Total de Equipes" color="text-indigo-400" :value="$totalTeams" />
            <x-dashboard-card label="Total de Eventos" color="text-cyan-400" :value="$totalEvents ?? count($events)" />
            <x-dashboard-card label="Média Equipes/Evento" color="text-indigo-400" :value="$totalEvents > 0 ? round($totalTeams / $totalEvents, 1) : 0" />
            <x-dashboard-card label="Eventos Concluídos" color="text-green-400" :value="$completedEvents" />
        </div>
        {{-- Chart --}}
        <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-lg p-5 flex flex-col">
            <h3 class="text-sm font-semibold text-gray-300 mb-3 uppercase tracking-wide">Equipes por Categoria</h3>
            @if (count($teamsByCategory) > 0)
                <div class="flex-1 min-h-[220px]">
                    <canvas id="teamsByCategoryChart" x-data="{}" x-init="
                        new Chart($el, {
                            type: 'bar',
                            data: {
                                labels: {{ $teamsByCategoryJson }}.map(d => d.label),
                                datasets: [{
                                    label: 'Equipes',
                                    data: {{ $teamsByCategoryJson }}.map(d => d.count),
                                    backgroundColor: ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#a78bfa', '#fb923c'],
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { ticks: { color: '#9ca3af', font: { size: 10 } }, grid: { display: false } },
                                    y: { ticks: { color: '#6b7280', font: { size: 10 }, stepSize: 1 }, grid: { color: '#1f2937' } }
                                }
                            }
                        })
                    "></canvas>
                </div>
            @else
                <p class="text-gray-500 text-sm">Nenhum dado disponível.</p>
            @endif
        </div>
    </div>

    {{-- Últimos Eventos --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-200 mb-4">Últimos Eventos</h3>
        @if (count($events ?? []) === 0)
            <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
                <span class="block font-bold">Nenhum evento cadastrado.</span>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($events as $event)
                    <a href="{{ route('tbr.event-detail', ['event_id' => $event['id']]) }}" wire:navigate
                        class="block group bg-gray-900/80 border border-gray-800 rounded-xl hover:border-gray-600 hover:bg-gray-900 transition-all duration-300">

                        <div class="px-5 pt-5 pb-3">
                            <div class="flex items-center justify-between">
                                <h2 class="text-base font-bold text-gray-100 truncate">{{ $event['name'] }}</h2>
                                @php
                                    $isConcluded = !empty($event['status']);
                                    $tipo = $event['tipo_evento'] ?? 'interno';
                                    $tipoBadgeColors = ['interno' => 'bg-gray-600', 'regional' => 'bg-blue-600', 'nacional' => 'bg-green-600'];
                                    $tipoColor = $tipoBadgeColors[$tipo] ?? 'bg-gray-600';
                                @endphp
                                <div class="flex gap-1 shrink-0 ml-2">
                                    <span class="text-xs font-bold uppercase tracking-wide px-2 py-0.5 rounded {{ $tipoColor }} text-white">{{ ucfirst($tipo) }}</span>
                                    <span class="text-xs font-bold uppercase tracking-wide px-2 py-0.5 rounded {{ $isConcluded ? 'bg-green-600' : 'bg-yellow-600' }} text-white">
                                        {{ $isConcluded ? 'Concluído' : 'Em Andamento' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-800"></div>

                        <div class="px-5 py-4 space-y-2.5 text-sm text-gray-400">
                            @if (!empty($event['date']))
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800/80 shrink-0">
                                        <i class="fas fa-calendar-alt text-blue-400 text-xs"></i>
                                    </div>
                                    <span>{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            @if (isset($event['teams_count']))
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800/80 shrink-0">
                                        <i class="fas fa-users text-gray-500 text-xs"></i>
                                    </div>
                                    <span>{{ $event['teams_count'] }} equipes</span>
                                </div>
                            @endif

                            @if (!empty($event['location']['municipio']) && !empty($event['location']['estado']))
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800/80 shrink-0">
                                        <i class="fas fa-map-marker-alt text-red-400 text-xs"></i>
                                    </div>
                                    <span>{{ $event['location']['municipio']['nome'] }} - {{ $event['location']['estado']['sigla'] }}</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

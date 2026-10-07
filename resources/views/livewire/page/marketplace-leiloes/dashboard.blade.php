<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Marketplace</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Anúncios" color="text-blue-400" :value="$totalAnuncios" />
        <x-dashboard-card label="Anúncios Ativos" color="text-green-400" :value="$anunciosAtivos" />
        <x-dashboard-card label="Encerrando Hoje" color="text-yellow-400" :value="$encerrandoHoje" />
        <x-dashboard-card label="Total em Lances" color="text-purple-400" value="R$ {{ number_format($totalEmLances, 2, ',', '.') }}" />
    </div>
</div>

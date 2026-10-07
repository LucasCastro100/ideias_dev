<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Mobilidade</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Motoristas" color="text-blue-400" :value="$totalDrivers" />
        <x-dashboard-card label="Motoristas Ativos" color="text-green-400" :value="$activeDrivers" />
        <x-dashboard-card label="Corridas Hoje" color="text-yellow-400" :value="$ridesToday" />
        <x-dashboard-card label="Faturamento do Mês" color="text-purple-400" value="R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}" />
    </div>
</div>

<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Advocacia</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-dashboard-card label="Total de Clientes" color="text-blue-400" :value="$totalClientes" />
        <x-dashboard-card label="Processos Ativos" color="text-green-400" :value="$processosAtivos" />
        <x-dashboard-card label="Audiências este Mês" color="text-yellow-400" :value="$audienciasEsteMes" />
        <x-dashboard-card label="Total de Honorários" color="text-cyan-400" value="R$ {{ number_format($totalHonorarios, 2, ',', '.') }}" />
    </div>
</div>

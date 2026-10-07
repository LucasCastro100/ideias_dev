<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Restaurante</h1>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Mesas" color="text-blue-400" :value="$totalMesas" />
        <x-dashboard-card label="Mesas Ocupadas" color="text-red-400" :value="$mesasOcupadas" />
        <x-dashboard-card label="Pedidos Abertos" color="text-yellow-400" :value="$pedidosAbertos" />
        <x-dashboard-card label="Faturamento do Dia" color="text-emerald-400" value="R$ {{ number_format($faturamentoDia, 2, ',', '.') }}" />
    </div>
</div>

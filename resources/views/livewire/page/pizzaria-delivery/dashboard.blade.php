<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Pizzaria</h1>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Produtos" color="text-blue-400" :value="$totalProdutos" />
        <x-dashboard-card label="Pedidos Hoje" color="text-yellow-400" :value="$pedidosHoje" />
        <x-dashboard-card label="Em Preparo" color="text-orange-400" :value="$pedidosEmPreparo" />
        <x-dashboard-card label="Faturamento do Dia" color="text-emerald-400" value="R$ {{ number_format($faturamentoDia, 2, ',', '.') }}" />
    </div>
</div>

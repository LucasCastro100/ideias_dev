<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard PDV</h1>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Produtos" color="text-blue-400" :value="$totalProdutos" />
        <x-dashboard-card label="Estoque Baixo" color="text-yellow-400" :value="$produtosEstoqueBaixo" />
        <x-dashboard-card label="Vendas Hoje" color="text-green-400" :value="$vendasHoje" />
        <x-dashboard-card label="Faturamento do Mês" color="text-emerald-400" value="R$ {{ number_format($faturamentoMes, 2, ',', '.') }}" />
    </div>
</div>

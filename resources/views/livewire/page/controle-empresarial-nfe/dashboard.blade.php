<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Controle Empresarial</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Produtos" color="text-blue-400" :value="$totalProdutos" />
        <x-dashboard-card label="Estoque Baixo" color="text-red-400" :value="$produtosEstoqueBaixo" />
        <x-dashboard-card label="Vendas do Mês" color="text-purple-400" :value="$vendasMes" />
        <x-dashboard-card label="Faturamento do Mês" color="text-green-400" value="R$ {{ number_format($faturamentoMes, 2, ',', '.') }}" />
    </div>
</div>

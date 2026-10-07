<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Marketing Multinível</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-dashboard-card label="Total de Membros" color="text-blue-400" :value="$totalMembros" />
        <x-dashboard-card label="Membros Ativos" color="text-green-400" :value="$membrosAtivos" />
        <x-dashboard-card label="Pagamentos Pendentes" color="text-yellow-400" :value="$pagamentosPendentes" />
        <x-dashboard-card label="Total Pago" color="text-cyan-400" value="R$ {{ number_format($totalPago, 2, ',', '.') }}" />
    </div>
</div>

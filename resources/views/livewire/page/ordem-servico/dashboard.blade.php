<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Ordem de Serviço</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Clientes" color="text-blue-400" :value="$totalClientes" />
        <x-dashboard-card label="OS Abertas" color="text-yellow-400" :value="$osAbertas" />
        <x-dashboard-card label="OS Concluídas no Mês" color="text-green-400" :value="$osConcluidasMes" />
        <x-dashboard-card label="Total a Receber" color="text-red-400" value="R$ {{ number_format($totalAReceber, 2, ',', '.') }}" />
    </div>
</div>

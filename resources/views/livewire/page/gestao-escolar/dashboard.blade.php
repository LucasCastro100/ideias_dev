<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Gestão Escolar</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Turmas" color="text-blue-400" :value="$totalTurmas" />
        <x-dashboard-card label="Alunos Ativos" color="text-green-400" :value="$totalAlunosAtivos" />
        <x-dashboard-card label="Faturas Pendentes" color="text-yellow-400" :value="$faturasPendentes" />
        <x-dashboard-card label="Recebido no Mês" color="text-purple-400" value="R$ {{ number_format($totalRecebidoMes, 2, ',', '.') }}" />
    </div>
</div>

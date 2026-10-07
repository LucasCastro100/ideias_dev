<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Clínica</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Pacientes" color="text-blue-400" :value="$totalPacientes" />
        <x-dashboard-card label="Consultas Hoje" color="text-green-400" :value="$consultasHoje" />
        <x-dashboard-card label="Consultas Este Mês" color="text-purple-400" :value="$consultasMes" />
        <x-dashboard-card label="Consultas Canceladas" color="text-red-400" :value="$consultasCanceladas" />
    </div>
</div>

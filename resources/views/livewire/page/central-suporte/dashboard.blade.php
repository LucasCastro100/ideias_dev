<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Central de Suporte</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Tickets" color="text-blue-400" :value="$totalTickets" />
        <x-dashboard-card label="Tickets Abertos" color="text-yellow-400" :value="$ticketsAbertos" />
        <x-dashboard-card label="Resolvidos este Mês" color="text-green-400" :value="$ticketsResolvidosMes" />
        <x-dashboard-card label="Departamentos" color="text-purple-400" :value="$totalDepartamentos" />
    </div>
</div>

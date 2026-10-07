<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Site Institucional</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Páginas Publicadas" color="text-green-400" :value="$totalPaginasPublicadas" />
        <x-dashboard-card label="Páginas em Rascunho" color="text-yellow-400" :value="$totalPaginasRascunho" />
        <x-dashboard-card label="Total de Leads" color="text-blue-400" :value="$totalLeads" />
        <x-dashboard-card label="Leads este Mês" color="text-purple-400" :value="$leadsMes" />
    </div>
</div>

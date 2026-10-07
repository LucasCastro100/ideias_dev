<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Vagas de Emprego</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Empresas" color="text-blue-400" :value="$totalCompanies" />
        <x-dashboard-card label="Vagas Abertas" color="text-green-400" :value="$totalOpenJobs" />
        <x-dashboard-card label="Candidatos" color="text-purple-400" :value="$totalJobSeekers" />
        <x-dashboard-card label="Candidaturas" color="text-yellow-400" :value="$totalApplications" />
    </div>
</div>

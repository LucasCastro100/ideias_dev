<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Armazenamento em Nuvem</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Pastas" color="text-blue-400" :value="$totalFolders" />
        <x-dashboard-card label="Total de Arquivos" color="text-green-400" :value="$totalFiles" />
        <x-dashboard-card label="Arquivos Públicos" color="text-yellow-400" :value="$publicFiles" />
        <x-dashboard-card label="Espaço Usado" color="text-purple-400" value="{{ number_format($usedSpaceMb, 2, ',', '.') }} MB" />
    </div>
</div>

<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard Rede Social</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard-card label="Total de Publicações" color="text-blue-400" :value="$totalPosts" />
        <x-dashboard-card label="Publicações este Mês" color="text-green-400" :value="$postsThisMonth" />
        <x-dashboard-card label="Total de Grupos" color="text-purple-400" :value="$totalGroups" />
        <x-dashboard-card label="Grupos Abertos" color="text-yellow-400" :value="$openGroups" />
    </div>
</div>

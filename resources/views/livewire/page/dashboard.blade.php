<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Dashboard</h1>
    </div>

    @php $user = auth()->user(); @endphp

    @if ($user->isSuperAdmin() || !$user->system_id)
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wide">Todos os Sistemas</h2>
                <div class="relative w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs"></i>
                    <input type="text" wire:model.live.debounce.300ms="systemsSearch" placeholder="Buscar sistema..."
                        class="w-full bg-gray-950 border border-gray-800 text-gray-200 text-sm rounded-lg pl-9 pr-3 py-2 placeholder-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7 gap-3">
                @forelse ($systemsOverviewList as $sys)
                    <a href="{{ route($sys['route']) }}" wire:navigate
                        class="group bg-gray-950 border border-gray-800 rounded-lg p-3 hover:border-blue-600 transition-all duration-200 flex flex-col items-center text-center">
                        <div class="w-9 h-9 rounded-lg bg-blue-600/10 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $sys['icon'] }} text-blue-400 text-sm"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-200 leading-tight">{{ $sys['label'] }}</span>
                        <span class="text-[11px] text-gray-500 mt-1">{{ $sys['count'] }} {{ $sys['countLabel'] }}</span>
                    </a>
                @empty
                    <div class="col-span-full text-center text-gray-500 text-sm py-6">Nenhum sistema encontrado.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>

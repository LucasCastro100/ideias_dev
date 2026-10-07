<div class="py-6" wire:poll.30s>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-200">Usuários</h2>
            <span class="text-xs text-gray-500">{{ $onlineCount }} online agora</span>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-4">
            <div class="flex items-center gap-4 flex-wrap">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nome ou e-mail..."
                    class="flex-1 min-w-[200px] bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="flex items-center gap-1.5 ml-auto">
                    <span class="text-xs text-gray-500">Qtd</span>
                    <select wire:model.live="perPage" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1.5 text-sm w-16">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 overflow-hidden shadow-xl rounded-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs uppercase bg-gray-800 text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Nome</th>
                            <th class="px-6 py-3">E-mail</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Última atividade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $user['online'] ? 'bg-green-500' : 'bg-gray-600' }}"></span>
                                        <span class="{{ $user['online'] ? 'text-green-400' : 'text-gray-500' }}">
                                            {{ $user['online'] ? 'Online' : 'Offline' }}
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-white">{{ $user['name'] }}</td>
                                <td class="px-6 py-4">{{ $user['email'] }}</td>
                                <td class="px-6 py-4">{{ $user['role'] }}</td>
                                <td class="px-6 py-4">{{ $user['last_seen'] ?? 'Nunca' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhum usuário encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <div class="text-xs text-gray-500">
                Mostrando {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} de {{ $users->total() }} usuários
            </div>
        </div>

        @if ($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

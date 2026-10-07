<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Empresas</h1>
            <button wire:click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition text-sm font-medium">
                + Nova Empresa
            </button>
        </div>
    </div>

    @if ($isSuperAdmin)
    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0">
                <input wire:model.live.debounce.300ms="search" placeholder="Buscar por nome da empresa..."
                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
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
    @endif

    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400 min-w-[600px]">
                <thead class="text-xs uppercase bg-gray-800">
                    <tr>
                        <th class="px-6 py-3">Empresa</th>
                        <th class="px-6 py-3">Proprietário</th>
                        <th class="px-6 py-3 text-center">Usuários</th>
                        <th class="px-6 py-3">Criada em</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($companies as $company)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4 text-white font-medium">{{ $company->name }}</td>
                            <td class="px-6 py-4">{{ $company->owner?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="openModal({{ $company->id }})"
                                    class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 transition">
                                    <span class="font-semibold">{{ $company->users_count }}</span>
                                    <i class="fas fa-users text-xs"></i>
                                </button>
                            </td>
                            <td class="px-6 py-4">{{ $company->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openModal({{ $company->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Ver usuários">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if ($isSuperAdmin)
                                    <button wire:click="confirmDelete({{ $company->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhuma empresa cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isSuperAdmin)
    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $companies->firstItem() ?? 0 }} a {{ $companies->lastItem() ?? 0 }} de {{ $companies->total() }} empresas
        </div>
    </div>

    @if ($companies->hasPages())
    <div class="mt-4">
        {{ $companies->links() }}
    </div>
    @endif
    @endif

    {{-- Users Modal --}}
    @if ($showModal && $selectedCompany)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">{{ $selectedCompany->name }}</h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-300 text-xl leading-none">&times;</button>
                </div>

                <p class="text-sm text-gray-400 mb-4">Usuários vinculados ({{ $selectedCompany->users->count() }})</p>

                <div class="space-y-2 max-h-64 overflow-y-auto mb-4">
                    @forelse ($selectedCompany->users as $user)
                        <div class="flex items-center gap-3 bg-gray-800/50 rounded-lg p-3">
                            <i class="fas fa-user text-gray-500"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 capitalize">{{ $user->pivot->role ?? 'Membro' }}</span>
                                <button wire:click="removeUser({{ $user->id }})" class="p-1 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded transition" title="Remover">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Nenhum usuário vinculado.</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-800 pt-4">
                    <p class="text-sm text-gray-400 mb-3">Convidar por email</p>
                    <div class="flex gap-2">
                        <input wire:model="inviteEmail" type="email" placeholder="email@exemplo.com"
                            class="flex-1 bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                        <button wire:click="sendInvitation" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition text-sm whitespace-nowrap">Enviar Convite</button>
                    </div>
                    @error('inviteEmail') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">Fechar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Create Company Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Nova Empresa</h3>
                    <button wire:click="closeCreateModal" class="text-gray-500 hover:text-gray-300 text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="saveCompany">
                    <div class="mb-4">
                        <label class="block text-sm text-gray-400 mb-1">Nome da Empresa</label>
                        <input wire:model="newCompanyName" placeholder="Ex: Elite Pilates"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                        @error('newCompanyName') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition text-sm">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />
</div>

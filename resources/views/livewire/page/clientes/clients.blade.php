<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Clientes</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Cliente">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Cliente</span>
            </button>
        </div>
    </div>

    @php
        $isSuper = auth()->user()->isSuperAdmin();
        $baseQuery = \App\Models\Client::query();
        if (!$isSuper) {
            $teamId = auth()->user()->teams()->first()?->id;
            $baseQuery = $teamId ? $baseQuery->where('team_id', $teamId) : $baseQuery->whereRaw('1 = 0');
        }
        $totalM = (clone $baseQuery)->where('sexo', 'M')->count();
        $totalF = (clone $baseQuery)->where('sexo', 'F')->count();
    @endphp

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0">
                <input wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, email, telefone ou documento..."
                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-900/30 border border-blue-800/50 text-blue-300 text-sm font-semibold">
                    <i class="fas fa-mars text-xs"></i>
                    <span>{{ $totalM }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-pink-900/30 border border-pink-800/50 text-pink-300 text-sm font-semibold">
                    <i class="fas fa-venus text-xs"></i>
                    <span>{{ $totalF }}</span>
                </span>
                <div class="flex items-center gap-1.5 pl-3 border-l border-gray-700">
                    <span class="text-xs text-gray-500 whitespace-nowrap">Qtd</span>
                    <select wire:model.live="perPage" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1.5 text-sm w-16">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400 min-w-[800px]">
                <thead class="text-xs uppercase bg-gray-800">
                    <tr>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 cursor-pointer select-none" wire:click="sortBy('name')">
                            <span class="inline-flex items-center gap-1">
                                Nome
                                @if ($sortField === 'name')
                                    <i class="fas fa-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-400"></i>
                                @else
                                    <i class="fas fa-sort text-gray-600"></i>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Telefone</th>
                        <th class="px-6 py-3">Documento</th>
                        <th class="px-6 py-3 cursor-pointer select-none" wire:click="sortBy('birth_date')">
                            <span class="inline-flex items-center gap-1">
                                Aniversário
                                @if ($sortField === 'birth_date')
                                    <i class="fas fa-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-400"></i>
                                @else
                                    <i class="fas fa-sort text-gray-600"></i>
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-3">Sexo</th>
                        <th class="px-6 py-3">Total Arrecadado</th>
                        @if (auth()->user()->isSuperAdmin())<th class="px-6 py-3">Empresa</th>@endif
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-gray-800/50 {{ !$client->active ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                @if ($client->active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900 text-red-300">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-white">{{ $client->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $client->email ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $client->phone ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $client->document ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $client->birth_date ? $client->birth_date->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($client->sexo === 'M')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-900/50 text-blue-400 text-xs font-bold" title="Masculino">♂</span>
                                @elseif ($client->sexo === 'F')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-900/50 text-pink-400 text-xs font-bold" title="Feminino">♀</span>
                                @else
                                    <span class="text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-green-400 font-medium">
                                R$ {{ number_format($client->total_revenue ?? 0, 2, ',', '.') }}
                            </td>
                            @if (auth()->user()->isSuperAdmin())
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $client->team?->name ?? $client->user?->name ?? '-' }}</td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openHistory({{ $client->id }})" class="p-1.5 text-purple-500 hover:text-purple-400 hover:bg-purple-500/10 rounded-lg transition" title="Histórico">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button wire:click="edit({{ $client->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if ($client->active)
                                        <button wire:click="confirmAction({{ $client->id }}, 'deactivate')" class="p-1.5 text-yellow-500 hover:text-yellow-400 hover:bg-yellow-500/10 rounded-lg transition" title="Desativar">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @else
                                        <button wire:click="activate({{ $client->id }})" class="p-1.5 text-green-500 hover:text-green-400 hover:bg-green-500/10 rounded-lg transition" title="Ativar">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    <button wire:click="confirmAction({{ $client->id }}, 'delete')" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 10 : 9 }}" class="px-6 py-12 text-gray-500">Nenhum cliente cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $clients->firstItem() ?? 0 }} a {{ $clients->lastItem() ?? 0 }} de {{ $clients->total() }} clientes
        </div>
    </div>

    @if ($clients->hasPages())
    <div class="mt-4">
        {{ $clients->links() }}
    </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $clientId ? 'Editar' : 'Novo' }} Cliente</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1 font-semibold">Nome</label>
                        <input wire:model="name" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1 font-semibold">Email</label>
                        <input type="email" wire:model="email" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                        @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1 font-semibold">Telefone</label>
                            <input wire:model="phone" maxlength="15" placeholder="(99) 99999-9999"
                                x-on:input="
                                    let val = $event.target.value.replace(/\D/g, '').substring(0, 11);
                                    val = val.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
                                    $event.target.value = val"
                                class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                            @error('phone') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1 font-semibold">Documento</label>
                            <input wire:model="document" maxlength="18" placeholder="CPF ou CNPJ"
                                x-on:input="
                                    let val = $event.target.value.replace(/\D/g, '').substring(0, 14);
                                    if (val.length <= 11) {
                                        val = val.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                                    } else {
                                        val = val.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
                                    }
                                    $event.target.value = val"
                                class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                            @error('document') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1 font-semibold">Data de Aniversário</label>
                            <input type="date" wire:model="birthDate" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                            @error('birthDate') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1 font-semibold">Sexo</label>
                            <div class="flex gap-6 mt-1">
                                <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                    <input type="radio" wire:model="sexo" value="M" class="text-blue-500 focus:ring-blue-500 bg-gray-800 border-gray-700">
                                    <span class="text-sm">Masculino</span>
                                </label>
                                <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                                    <input type="radio" wire:model="sexo" value="F" class="text-pink-500 focus:ring-pink-500 bg-gray-800 border-gray-700">
                                    <span class="text-sm">Feminino</span>
                                </label>
                            </div>
                            @error('sexo') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-4" x-data="{ cepConsultado: $wire.endereco !== '' }">
                        <h4 class="text-sm font-semibold text-gray-200 mb-3">Endereço</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-1">
                                <label class="block text-sm text-gray-300 mb-1 font-semibold">CEP</label>
                                <input wire:model="cep" maxlength="10" placeholder="00000-000"
                                    x-on:input="
                                        let val = $event.target.value.replace(/\D/g, '').substring(0, 8);
                                        if (val.length > 5) { val = val.replace(/^(\d{5})(\d)/, '$1-$2'); }
                                        $event.target.value = val"
                                    x-on:blur="
                                        let cep = $el.value.replace(/\D/g, '');
                                        if (cep.length === 8) {
                                            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                                                .then(r => r.json())
                                                .then(d => {
                                                    if (!d.erro) {
                                                        $wire.set('endereco', d.logradouro || '');
                                                        $wire.set('bairro', d.bairro || '');
                                                        $wire.set('cidade', d.localidade || '');
                                                        $wire.set('uf', d.uf || '');
                                                        cepConsultado = true;
                                                    }
                                                });
                                        }"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                @error('cep') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm text-gray-300 mb-1">Logradouro</label>
                                <input wire:model="endereco" placeholder="Rua, Avenida..." :disabled="!cepConsultado"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                @error('endereco') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Número</label>
                                <input wire:model="numero" placeholder="Nº" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                @error('numero') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Bairro</label>
                                <input wire:model="bairro" placeholder="Bairro" :disabled="!cepConsultado"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                @error('bairro') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Cidade</label>
                                <input wire:model="cidade" placeholder="Cidade" :disabled="!cepConsultado"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                @error('cidade') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">UF</label>
                                <input wire:model="uf" placeholder="UF" maxlength="2" :disabled="!cepConsultado"
                                    class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                @error('uf') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm text-gray-300 mb-1">Complemento</label>
                                <input wire:model="complemento" placeholder="Apto, Bloco, etc." class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                                @error('complemento') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-4">
                        <label class="block text-sm text-gray-300 mb-2 font-semibold">Observações</label>
                        <div wire:ignore class="border border-gray-700 rounded-lg overflow-hidden">
                            <div class="flex flex-wrap gap-0.5 p-2 bg-gray-800 border-b border-gray-700">
                                <button type="button" onclick="let ed=document.getElementById('notesEditor');ed.focus();document.execCommand('formatBlock', false, '<p>')||document.execCommand('formatBlock', false, 'p')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Parágrafo">P</button>
                                <button type="button" onclick="let ed=document.getElementById('notesEditor');ed.focus();document.execCommand('formatBlock', false, '<h1>')||document.execCommand('formatBlock', false, 'h1')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 1">H1</button>
                                <button type="button" onclick="let ed=document.getElementById('notesEditor');ed.focus();document.execCommand('formatBlock', false, '<h2>')||document.execCommand('formatBlock', false, 'h2')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 2">H2</button>
                                <button type="button" onclick="let ed=document.getElementById('notesEditor');ed.focus();document.execCommand('formatBlock', false, '<h3>')||document.execCommand('formatBlock', false, 'h3')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 3">H3</button>
                                <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('bold')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Negrito"><b>B</b></button>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('italic')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded italic" title="Itálico"><i>I</i></button>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('underline')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded underline" title="Sublinhado"><u>U</u></button>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('strikeThrough')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded line-through" title="Tachado"><s>S</s></button>
                                <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('insertUnorderedList')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Lista">☰</button>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('insertOrderedList')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Lista numerada">#</button>
                                <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
                                <button type="button" onclick="const e=document.getElementById('notesEditor');e.focus();document.execCommand('removeFormat')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Limpar formatação">✕</button>
                            </div>
                            <div id="notesEditor" contenteditable="true"
                                x-data="{ init() { $el.innerHTML = $wire.notes; $el.addEventListener('input', () => $wire.set('notes', $el.innerHTML)); } }"
                                class="bg-gray-900 text-gray-200 p-4 min-h-[200px] focus:outline-none overflow-y-auto leading-relaxed"
                                style="white-space: pre-wrap; word-wrap: break-word;"
                                placeholder="Observações sobre o cliente..."></div>
                        </div>
                        @error('notes') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showHistoryModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Histórico — {{ $historyClientName }}</h3>
                    <button wire:click="closeHistory" class="text-gray-500 hover:text-gray-300 text-xl leading-none">&times;</button>
                </div>

                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-300 uppercase tracking-wide mb-3">Aulas</h4>
                    @if (count($historyLogs))
                        <div class="space-y-2">
                            @foreach ($historyLogs as $log)
                                <div class="flex items-start gap-3 bg-gray-800/50 rounded-lg p-3">
                                    <i class="fas fa-chalkboard text-blue-400 mt-1"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white">{!! $log['description'] !!}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($log['lesson_date'])->format('d/m/Y') }} — {{ $log['user']['name'] ?? '-' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Nenhuma aula registrada.</p>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-300 uppercase tracking-wide mb-3">Recebimentos</h4>
                    @if (count($historyAccounts))
                        <div class="space-y-2">
                            @foreach ($historyAccounts as $acc)
                                <div class="flex items-start gap-3 bg-gray-800/50 rounded-lg p-3">
                                    @php
                                        $isOverdue = !$acc['paid'] && $acc['due_date'] && \Carbon\Carbon::parse($acc['due_date'])->isPast();
                                    @endphp
                                    <i class="fas {{ $acc['paid'] ? 'fa-check-circle text-green-400' : ($isOverdue ? 'fa-exclamation-circle text-red-400' : 'fa-clock text-yellow-500') }} mt-1"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white">R$ {{ number_format($acc['value'], 2, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ str_pad($acc['month'], 2, '0', STR_PAD_LEFT) }}/{{ $acc['year'] }} — {{ $acc['paid'] ? 'Pago' : ($isOverdue ? 'Atrasado' : 'Pendente') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Nenhum recebimento registrado.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @push('styles')
    <style>
        #notesEditor:empty:before {
            content: attr(placeholder);
            color: #6b7280;
            pointer-events: none;
        }
        #notesEditor h1 { font-size: 1.5rem; font-weight: 700; color: #f3f4f6; margin: 0.5em 0; }
        #notesEditor h2 { font-size: 1.25rem; font-weight: 600; color: #f3f4f6; margin: 0.5em 0; }
        #notesEditor h3 { font-size: 1.125rem; font-weight: 600; color: #f3f4f6; margin: 0.5em 0; }
        #notesEditor p { margin: 0.5em 0; }
        #notesEditor ul, #notesEditor ol { padding-left: 1.5em; margin: 0.5em 0; }
        #notesEditor li { margin: 0.25em 0; }
        #notesEditor strong { color: #f9fafb; }
    </style>
    @endpush
</div>

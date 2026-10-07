<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-100">Registro de Aulas</h1>
            <button wire:click="openModal" class="px-2 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Novo Registro">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline sm:ml-1.5">Novo Registro</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dia</label>
                <select wire:model.live="filterDay" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach (range(1, 31) as $d)
                        <option value="{{ $d }}">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Mês</label>
                <select wire:model.live="filterMonth" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Ano</label>
                <select wire:model.live="filterYear" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
                    @foreach (range(now()->year - 5, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            @if (auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-xs text-gray-500 mb-1">Cliente</label>
                <select wire:model.live="filterClientId" class="bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach ($filterableClients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
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

    <div class="space-y-4">
        @forelse ($logs as $log)
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm font-medium text-white">{{ $log->client?->name ?? '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $log->lesson_date?->format('d/m/Y') ?? '-' }}</span>
                            @if (auth()->user()->isSuperAdmin())
                            <span class="text-xs text-gray-600">— {{ $log->team?->name ?? $log->user?->name ?? '-' }}</span>
                            @endif
                        </div>
                        <div class="prose prose-sm prose-invert max-w-none text-gray-300">
                            {!! $log->description !!}
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="edit({{ $log->id }})" class="p-1.5 text-blue-500 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $log->id }})" class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-12 text-center text-gray-500">
                Nenhum registro de aula encontrado.
            </div>
        @endforelse
    </div>

    <div class="flex items-center justify-end mt-4">
        <div class="text-xs text-gray-500">
            Mostrando {{ $logs->firstItem() ?? 0 }} a {{ $logs->lastItem() ?? 0 }} de {{ $logs->total() }} registros
        </div>
    </div>

    @if ($logs->hasPages())
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
    @endif

    <x-confirm-dialog :id="$confirmingId" :message="$confirmingMessage" />

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-white mb-4">{{ $logId ? 'Editar' : 'Novo' }} Registro de Aula</h3>
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Cliente</label>
                            <x-searchable-select model="client_id" :options="$clients->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" placeholder="Selecione um cliente" :initial="$client_id" />
                            @error('client_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Data da Aula</label>
                            <input type="date" wire:model="lesson_date" class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('lesson_date') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Descrição da Aula</label>
                        <div wire:ignore class="border border-gray-700 rounded-lg overflow-hidden">
                            <div class="flex flex-wrap gap-0.5 p-2 bg-gray-800 border-b border-gray-700">
                                <button type="button" onclick="let ed=document.getElementById('descriptionEditor');ed.focus();document.execCommand('formatBlock', false, '<p>')||document.execCommand('formatBlock', false, 'p')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Parágrafo">P</button>
                                <button type="button" onclick="let ed=document.getElementById('descriptionEditor');ed.focus();document.execCommand('formatBlock', false, '<h1>')||document.execCommand('formatBlock', false, 'h1')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 1">H1</button>
                                <button type="button" onclick="let ed=document.getElementById('descriptionEditor');ed.focus();document.execCommand('formatBlock', false, '<h2>')||document.execCommand('formatBlock', false, 'h2')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 2">H2</button>
                                <button type="button" onclick="let ed=document.getElementById('descriptionEditor');ed.focus();document.execCommand('formatBlock', false, '<h3>')||document.execCommand('formatBlock', false, 'h3')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 3">H3</button>
                                <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('bold')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Negrito"><b>B</b></button>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('italic')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded italic" title="Itálico"><i>I</i></button>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('underline')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded underline" title="Sublinhado"><u>U</u></button>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('strikeThrough')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded line-through" title="Tachado"><s>S</s></button>
                                <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('insertUnorderedList')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Lista">☰</button>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('insertOrderedList')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Lista numerada">#</button>
                                <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
                                <button type="button" onclick="const e=document.getElementById('descriptionEditor');e.focus();document.execCommand('removeFormat')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Limpar formatação">✕</button>
                            </div>
                            <div id="descriptionEditor" contenteditable="true"
                                x-data="{ init() { $el.innerHTML = $wire.description; $el.addEventListener('input', () => $wire.set('description', $el.innerHTML)); } }"
                                class="bg-gray-900 text-gray-200 p-4 min-h-[250px] focus:outline-none overflow-y-auto leading-relaxed"
                                style="white-space: pre-wrap; word-wrap: break-word;"
                                placeholder="Descreva o que foi dado na aula..."></div>
                        </div>
                        @error('description') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('styles')
    <style>
        #descriptionEditor:empty:before {
            content: attr(placeholder);
            color: #6b7280;
            pointer-events: none;
        }
        #descriptionEditor h1 { font-size: 1.5rem; font-weight: 700; color: #f3f4f6; margin: 0.5em 0; }
        #descriptionEditor h2 { font-size: 1.25rem; font-weight: 600; color: #f3f4f6; margin: 0.5em 0; }
        #descriptionEditor h3 { font-size: 1.125rem; font-weight: 600; color: #f3f4f6; margin: 0.5em 0; }
        #descriptionEditor p { margin: 0.5em 0; }
        #descriptionEditor ul, #descriptionEditor ol { padding-left: 1.5em; margin: 0.5em 0; }
        #descriptionEditor li { margin: 0.25em 0; }
        #descriptionEditor strong { color: #f9fafb; }
    </style>
    @endpush
</div>

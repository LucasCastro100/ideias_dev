<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            @if ($selectedTeam)
                <button wire:click="backToList"
                    class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                    <i class="fas fa-arrow-left"></i>
                </button>
            @else
                <a href="{{ route('tbr.event-detail', ['event_id' => $eventId]) }}"
                    class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
            @endif
            <div>
                <h1 class="text-2xl font-semibold text-gray-100">
                    @if ($selectedTeam)
                        {{ $selectedTeam->name }}
                    @else
                        Equipes - {{ $eventName ?? 'Evento não encontrado' }}
                    @endif
                </h1>
                @if ($selectedTeam)
                    <span class="text-sm text-gray-400 mt-1 block">Detalhes da equipe</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($selectedTeam)
                <a href="{{ route('tbr.ranking.teamPdf', ['event_id' => $eventId, 'team_id' => $selectedTeam->id]) }}"
                    class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition text-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Gerar PDF
                </a>
                @can('create-event')
                    <a href="{{ route('tbr.edit-scores', ['event_id' => $eventId, 'equipe' => $selectedTeam->id]) }}"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition text-sm">
                        <i class="fas fa-pen-to-square mr-1"></i> Editar Notas
                    </a>
                @endcan
                @can('edit')
                    <button wire:click="openEditModal('{{ $selectedTeam->id }}')"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition text-sm">
                        <i class="fas fa-pen mr-1"></i> Editar
                    </button>
                @endcan
                @can('delete')
                    <button wire:click="openDeleteModal('{{ $selectedTeam->id }}')"
                        class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition text-sm">
                        <i class="fas fa-trash mr-1"></i> Excluir
                    </button>
                @endcan
            @endif
        </div>
    </div>

    @if ($selectedTeam && $selectedTeamId)
        {{-- Detalhes da equipe --}}
        <div class="grid gap-6 grid-cols-1 lg:grid-cols-3">
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <h3 class="text-lg font-semibold text-gray-100 mb-4">Informações</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500 block">Nome</span>
                            <span class="text-gray-200 font-medium">{{ $selectedTeam->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Categoria</span>
                            <span class="text-gray-200 font-medium">{{ ucfirst($selectedTeam->category_slug) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Pontuação Total</span>
                            <span class="text-yellow-400 font-bold text-lg">{{ round($selectedTeam->total_score) }}</span>
                        </div>

                        @if ($selectedTeam->representative_name)
                            <hr class="border-gray-800">
                            <div class="text-gray-300 font-semibold">Representante</div>
                            <div>
                                <span class="text-gray-500 block">Nome</span>
                                <span class="text-gray-200">{{ $selectedTeam->representative_name }}</span>
                            </div>
                            @if ($selectedTeam->representative_email)
                                <div>
                                    <span class="text-gray-500 block">E-mail</span>
                                    <a href="mailto:{{ $selectedTeam->representative_email }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $selectedTeam->representative_email }}
                                    </a>
                                </div>
                            @endif
                            @if ($selectedTeam->representative_phone)
                                <div>
                                    <span class="text-gray-500 block">Telefone</span>
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $selectedTeam->representative_phone) }}" target="_blank"
                                        class="text-green-400 hover:text-green-300">
                                        <i class="fab fa-whatsapp mr-1"></i>{{ $selectedTeam->representative_phone }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <h3 class="text-lg font-semibold text-gray-100 mb-4">Notas por Modalidade</h3>

                    @php
                        $groupedScores = $selectedTeam->scores->groupBy('modality_slug');
                    @endphp

                    @forelse ($groupedScores as $modSlug => $modScores)
                        <div class="border border-gray-800 rounded-lg p-4 mb-3">
                            @if ($modSlug === 'dp')
                                @php
                                    $bestRound = $modScores->sortByDesc('total')->first();
                                    $totalGeral = $selectedTeam->total_score;
                                @endphp
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold text-gray-200 uppercase">DP — Desafio Prático</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('tbr.ranking.teamModalityPdf', ['event_id' => $eventId, 'team_id' => $selectedTeam->id, 'modality' => $modSlug]) }}"
                                        class="text-green-400 hover:text-green-300 transition text-sm" title="Baixar PDF da modalidade">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </a>
                                    <span class="text-yellow-400 font-bold">Melhor: {{ round($bestRound?->total ?? 0) }}</span>
                                </div>
                            </div>
                                <div class="space-y-2">
                                    @foreach (['r1', 'r2', 'r3'] as $round)
                                        @php
                                            $roundScore = $modScores->firstWhere('round', $round);
                                            $isBest = $roundScore && $bestRound && $roundScore->id === $bestRound->id;
                                        @endphp
                                        <div class="flex items-center gap-3 text-sm {{ $isBest ? 'bg-blue-900/20 border border-blue-800/50 rounded-lg p-2' : '' }}">
                                            <span class="text-gray-500 font-mono w-6">{{ $round }}:</span>
                                            @if ($roundScore && !empty($roundScore->scores))
                                                <span class="text-gray-300">
                                                    @foreach ($roundScore->scores as $i => $s)
                                                        <span class="{{ $isBest ? 'font-bold text-blue-300' : '' }}">{{ round($s) }}</span>@if ($i < count($roundScore->scores) - 1)<span class="text-gray-600"> — </span>@endif
                                                    @endforeach
                                                </span>
                                                <span class="ml-auto text-gray-500">({{ round($roundScore->total) }})</span>
                                            @else
                                                <span class="text-gray-600">—</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                @php
                                    $firstScore = $modScores->first();
                                @endphp
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-200 uppercase">{{ $modSlug }}</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('tbr.ranking.teamModalityPdf', ['event_id' => $eventId, 'team_id' => $selectedTeam->id, 'modality' => $modSlug]) }}"
                                        class="text-green-400 hover:text-green-300 transition text-sm" title="Baixar PDF da modalidade">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </a>
                                    <span class="text-yellow-400 font-bold">Total: {{ round($firstScore?->total ?? 0) }}</span>
                                </div>
                            </div>
                                @if ($firstScore && !empty($firstScore->scores))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($firstScore->scores as $s)
                                            <span class="bg-gray-800 text-gray-300 text-xs px-2 py-1 rounded">{{ round($s) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-600 text-sm">Nenhuma nota registrada</span>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nenhuma nota encontrada para esta equipe.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        {{-- Lista de equipes --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="p-4 border-b border-gray-800 flex flex-wrap items-center gap-4">
                <select wire:model.live="filterCategory"
                    class="bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas as categorias</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->label }}</option>
                    @endforeach
                </select>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($categories as $cat)
                        @php $count = $categoryCounts[$cat->slug] ?? 0; @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide
                            {{ $count > 0 ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300' }}">
                            {{ $cat->label }}
                            <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 rounded-md bg-white text-gray-900 text-[11px] font-extrabold">{{ $count }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-800/50 text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 ">Equipe</th>
                            <th class="px-5 py-3 ">Categoria</th>
                            <th class="px-5 py-3 ">Representante</th>
                            <th class="px-5 py-3 ">Acompanhamento</th>
                            <th class="px-5 py-3 text-right">Ações</th>
                            <th class="px-5 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($teams as $team)
                            <tr class="hover:bg-gray-800/40 transition">
                                <td class="px-5 py-3 font-medium text-gray-200">{{ $team->name }}</td>
                                <td class="px-5 py-3">
                                    <span class="bg-gray-800 text-gray-300 text-xs px-2 py-1 rounded">{{ ucfirst($team->category_slug) }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-400">
                                    {{ $team->representative_name ?: '-' }}
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $teamScoresGrouped = $team->scores->groupBy('modality_slug');
                                    @endphp
                                    <div class="flex flex-wrap gap-x-3 gap-y-1">
                                        @foreach (['mc', 'om', 'te'] as $mod)
                                            @php
                                                $modScore = $teamScoresGrouped->get($mod)?->first();
                                                $temNota = $modScore && !empty($modScore->scores);
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-xs">
                                                <span class="uppercase text-gray-500 font-semibold">{{ $mod }}</span>
                                                <span class="w-2.5 h-2.5 rounded-full inline-block {{ $temNota ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            </span>
                                        @endforeach
                                        @foreach (['r1', 'r2', 'r3'] as $round)
                                            @php
                                                $dpScore = $teamScoresGrouped->get('dp')?->firstWhere('round', $round);
                                                $temNota = $dpScore && !empty($dpScore->scores);
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-xs">
                                                <span class="font-mono text-gray-500 font-semibold">{{ $round }}</span>
                                                <span class="w-2.5 h-2.5 rounded-full inline-block {{ $temNota ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-right text-yellow-400 font-semibold">
                                    {{ round($team->total_score) }}
                                </td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="selectTeam('{{ $team->id }}')"
                                            class="text-blue-400 hover:text-blue-300 transition text-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @can('edit')
                                            <button wire:click="openEditModal('{{ $team->id }}')"
                                                class="text-yellow-400 hover:text-yellow-300 transition text-sm">
                                                <i class="fas fa-pen mr-1"></i>
                                            </button>
                                        @endcan
                                        @can('delete')
                                            <button wire:click="openDeleteModal('{{ $team->id }}')"
                                                class="text-red-400 hover:text-red-300 transition text-sm">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8  text-gray-500">
                                    Nenhuma equipe cadastrada neste evento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($showEditModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <form wire:submit.prevent="updateTeam"
                class="bg-gray-900 border border-gray-800 p-6 rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto space-y-6">
                <h2 class="text-xl font-bold text-gray-100 ">Editar Equipe</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold text-gray-300 text-sm mb-1">Nome da equipe</label>
                        <input type="text" wire:model.defer="editTeamName"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        @error('editTeamName')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-300 text-sm mb-1">Categoria</label>
                        <x-searchable-select model="editTeamCategory" :options="$categories->map(fn($c) => ['value' => $c->slug, 'label' => $c->label])->toArray()" placeholder="Selecione a categoria" :initial="$editTeamCategory" />
                        @error('editTeamCategory')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-300 text-sm mb-1">Representante</label>
                        <input type="text" wire:model.defer="editTeamRepresentative"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                            placeholder="Nome do representante">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-300 text-sm mb-1">E-mail</label>
                        <input type="email" wire:model.defer="editTeamEmail"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                            placeholder="E-mail do representante">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-300 text-sm mb-1">Telefone</label>
                        <input type="text" wire:model.defer="editTeamPhone"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                            placeholder="Telefone do representante">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeEditModal"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">Fechar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition">Salvar</button>
                </div>

                <div wire:loading wire:target="updateTeam" class="w-full p-2 ">
                    <span class="text-blue-400 font-semibold">Salvando...</span>
                </div>
            </form>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="fixed p-6 inset-0 bg-black/70 flex items-center justify-center z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 w-full max-w-md ">
                <h2 class="text-xl font-bold mb-4 text-red-400">Confirmar Exclusão</h2>
                <p class="mb-6 text-gray-300">Tem certeza que deseja excluir esta equipe?</p>

                <div class="flex justify-center gap-3">
                    <button type="button" wire:click="closeDeleteModal"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">Cancelar</button>
                    <button type="button" wire:click="deleteTeam"
                        class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition">Excluir</button>
                </div>

                <div wire:loading wire:target="deleteTeam" class="w-full p-2 ">
                    <span class="text-red-400 font-semibold">Excluindo...</span>
                </div>
            </div>
        </div>
    @endif
</div>

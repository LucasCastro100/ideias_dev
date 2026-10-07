<div>
    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            @if ($selectedTeam)
                <button wire:click="backToTeamList"
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
                        Editando Notas - {{ $selectedTeam->name }}
                    @else
                        Editar Notas - {{ $event?->name ?? 'Evento não encontrado' }}
                    @endif
                </h1>
                @if ($selectedTeam)
                    <span class="text-sm text-gray-400 mt-1 block">
                        Pontuação Geral: <strong class="text-yellow-400">{{ round($editTotalScore) }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if (!$event)
        <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
            <span class="block font-bold">Evento não encontrado.</span>
        </div>
    @elseif (!$selectedTeam)
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-800/50 text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3">Equipe</th>
                            <th class="px-5 py-3">Categoria</th>
                            <th class="px-5 py-3 text-right">Total</th>
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
                                <td class="px-5 py-3 text-right text-yellow-400 font-semibold">
                                    {{ round($team->total_score) }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button wire:click="selectTeam('{{ $team->id }}')"
                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition text-sm">
                                        <i class="fas fa-pen mr-1"></i> Editar Notas
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-gray-500">
                                    Nenhuma equipe cadastrada neste evento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($selectedTeam->scores->groupBy('modality_slug') as $modSlug => $modScores)
                @php
                    $modConfig = collect($modalitiesConfig)->firstWhere('slug', $modSlug);
                    $modLabel = $modConfig['label'] ?? strtoupper($modSlug);
                @endphp
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-100">{{ $modLabel }}</h3>
                        @if ($modSlug !== 'dp')
                            <button wire:click="resetRoundScores('{{ $modSlug }}', 'default')"
                                wire:confirm="Tem certeza? Isso vai resetar todas as notas desta modalidade."
                                class="text-xs px-2 py-1 bg-red-800 hover:bg-red-700 text-red-200 rounded transition">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                        @endif
                    </div>

                    @if ($modSlug === 'dp')
                        @foreach ($modScores as $score)
                            @php
                                $round = $score->round ?? 'default';
                                $scoresData = $editScores[$modSlug][$round] ?? ['scores' => [], 'total' => 0, 'comment' => ''];
                            @endphp
                            <div class="border border-gray-800 rounded-lg p-4 mb-3">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-400 uppercase">Round {{ strtoupper($round) }}</h4>
                                    <button wire:click="resetRoundScores('{{ $modSlug }}', '{{ $round }}')"
                                        wire:confirm="Tem certeza? Isso vai resetar todas as notas deste round."
                                        class="text-xs px-2 py-1 bg-red-800 hover:bg-red-700 text-red-200 rounded transition">
                                        <i class="fas fa-undo mr-1"></i> Reset
                                    </button>
                                </div>
                                @foreach ($scoresData['scores'] as $mIndex => $missionScore)
                                    <div class="mb-2 flex items-center gap-3">
                                        <span class="text-gray-500 text-sm w-8">M{{ $mIndex + 1 }}:</span>
                                        <input type="number" step="1" min="0"
                                            wire:model.live="editScores.{{ $modSlug }}.{{ $round }}.scores.{{ $mIndex }}"
                                            class="w-20 bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm text-center">
                                    </div>
                                @endforeach
                                <div class="mt-2 text-sm">
                                    <span class="text-gray-500">Subtotal:</span>
                                    <span class="text-yellow-400 font-bold ml-2">{{ round($scoresData['total']) }}</span>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-gray-500 text-sm mb-1">Comentário</label>
                                    <textarea wire:model="editScores.{{ $modSlug }}.{{ $round }}.comment"
                                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm"
                                        rows="2"></textarea>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @php
                            $firstScore = $modScores->first();
                            $round = 'default';
                            $scoresData = $editScores[$modSlug][$round] ?? ['scores' => [], 'total' => 0, 'comment' => ''];
                            $assessment = $questionsConfig[$modSlug] ?? [];
                        @endphp
                        @if (!empty($assessment))
                            <div class="space-y-3">
                                @php $noteIndex = 0; @endphp
                                @foreach ($assessment as $block)
                                    <div class="border border-gray-800 rounded-lg p-3">
                                        <h4 class="text-sm font-semibold text-gray-400 mb-2">{{ $block['object'] ?? '' }}</h4>
                                        @foreach ($block['description'] ?? [] as $desc)
                                            <div class="flex items-center gap-3 mb-1.5">
                                                <span class="text-gray-500 text-xs flex-1">{{ $desc }}</span>
                                                <input type="number" step="1" min="0" max="9"
                                                    wire:model.live="editScores.{{ $modSlug }}.{{ $round }}.scores.{{ $noteIndex }}"
                                                    class="w-16 bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm text-center">
                                            </div>
                                            @php $noteIndex++; @endphp
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @foreach ($scoresData['scores'] as $sIndex => $s)
                                <div class="flex items-center gap-3 mb-1.5">
                                    <span class="text-gray-500 text-sm w-8">#{{ $sIndex + 1 }}:</span>
                                    <input type="number" step="1" min="0"
                                        wire:model.live="editScores.{{ $modSlug }}.{{ $round }}.scores.{{ $sIndex }}"
                                        class="w-16 bg-gray-800 border border-gray-700 text-gray-200 rounded px-2 py-1 text-sm text-center">
                                </div>
                            @endforeach
                        @endif
                        <div class="mt-3 text-sm">
                            <span class="text-gray-500">Total:</span>
                            <span class="text-yellow-400 font-bold ml-2">{{ round($scoresData['total']) }}</span>
                        </div>
                        <div class="mt-2">
                            <label class="block text-gray-500 text-sm mb-1">Comentário</label>
                            <textarea wire:model="editScores.{{ $modSlug }}.{{ $round }}.comment"
                                class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded px-3 py-2 text-sm"
                                rows="2"></textarea>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="flex justify-end gap-3">
                <button wire:click="backToTeamList"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                    Cancelar
                </button>
                <button wire:click="saveScores"
                    class="px-6 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition">
                    <i class="fas fa-save mr-1"></i> Salvar Notas
                </button>
            </div>

            <div wire:loading wire:target="saveScores" class="w-full p-2 text-center">
                <span class="text-blue-400 font-semibold">Salvando...</span>
            </div>
        </div>
    @endif
</div>

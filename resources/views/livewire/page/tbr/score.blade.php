<div x-on:reload-page.window="window.location.reload()">
    @if ($event && $category && $modality)
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('tbr.link', ['event_id' => $event_id]) }}"
                        class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endauth
                <div>
                    <h1 class="text-2xl font-semibold text-gray-100">{{ $event->name ?? '—' }}</h1>
                    <p class="text-sm text-gray-400 mt-1">Painel de Notas</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-400 mb-1">Categoria</h2>
                <p class="text-lg font-bold text-gray-100">{{ $category['label'] ?? strtoupper($category_id) }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-400 mb-1">Modalidade</h2>
                <p class="text-lg font-bold text-gray-100">{{ $modality['label'] ?? strtoupper($modality_id) }}</p>
            </div>
        </div>

        @if (!$event->status)
            @if (!empty($filteredTeams) && $question)
                <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
                    <h2 class="text-xl font-bold text-gray-100 mb-4">Selecione uma equipe</h2>
                    <x-searchable-select
                        wire:key="team-select-{{ collect($filteredTeams)->pluck('id')->implode('-') }}"
                        model="selectedTeamId"
                        :options="collect($filteredTeams)->map(fn($t) => ['value' => $t['id'], 'label' => $t['name']])->toArray()"
                        placeholder="Selecione uma equipe"
                        :initial="$selectedTeamId" />
                </div>

                <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl">
                    @if ($modalitieSlug == 'dp')
                        <div class="flex items-center justify-between bg-blue-900/30 border border-blue-800/50 rounded-xl p-5 mb-6">
                            <div>
                                <h3 class="text-sm font-bold text-blue-300 uppercase tracking-wider">Pontuação Atual em Tela</h3>
                                <p class="text-xs text-blue-400/80 mt-1">Calculada automaticamente conforme as marcações dos juízes.</p>
                            </div>
                            <div class="text-2xl font-black text-blue-300 bg-gray-800 border border-blue-800/50 px-4 py-1.5 rounded-lg">
                                {{ $dp_pontos }} <span class="text-xs font-normal text-gray-400">pts</span>
                            </div>
                        </div>
                    @endif

                    <h2 class="text-xl font-bold text-gray-100 mb-4">Critérios de Avaliação</h2>

                    @if ($hasAssessment)
                        @if ($modalitieSlug == 'dp')
                            @foreach ($question as $blockIndex => $block)
                                @php $isLocked = $lockedBlocks[$blockIndex] ?? false; @endphp
                                <div class="mb-6 p-5 border border-gray-800 rounded-xl bg-gray-800/50 {{ $isLocked ? 'opacity-50' : '' }}">
                                    <div class="mb-4">
                                        <div class="flex items-center gap-2">
                                            @if ($isLocked)
                                                <i class="fas fa-lock text-red-400 text-xs" title="Disponível apenas após completar '{{ $block['depends_on'] ?? '' }}'"></i>
                                            @endif
                                            <span class="text-xs font-bold uppercase tracking-wider text-blue-300 bg-blue-900/30 px-2.5 py-1 rounded-md">
                                                {{ $block['mission'] }}
                                            </span>
                                        </div>
                                        @if(!empty($block['description']))
                                            <p class="text-sm text-gray-400 mt-1">{{ $block['description'] }}</p>
                                        @endif
                                        @if ($isLocked && !empty($block['depends_on']))
                                            <p class="text-xs text-red-400 mt-1">
                                                <i class="fas fa-lock mr-1"></i>Disponível apenas após pontuar "{{ $block['depends_on'] }}"
                                            </p>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach ($block['itens'] as $itemIndex => $item)
                                            <div class="bg-gray-900 p-5 rounded-xl border border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-200 text-base">{{ $item['name'] }}</h4>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Modo: {{ $item['type'] }}</span>
                                                        @if(isset($item['has_max']) && $item['has_max'])
                                                            <span class="text-xs font-bold text-red-400 bg-red-900/30 px-1.5 py-0.5 rounded border border-red-800/50">
                                                                Limite Máx: {{ $item['max_value'] }} objetos
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-wrap items-center gap-4">
                                                    @if ($item['type'] === 'radio')
                                                        <div class="flex flex-wrap gap-3">
                                                            @foreach ($item['options'] as $optIndex => $option)
                                                                <label class="flex items-center gap-2 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-md cursor-pointer hover:bg-gray-700 transition-colors select-none">
                                                                    <input type="radio" 
                                                                           name="dp_radio_{{ $blockIndex }}_{{ $itemIndex }}"
                                                                           value="{{ $option['value'] }}"
                                                                           wire:model="scores.{{ $blockIndex }}.{{ $itemIndex }}"
                                                                           wire:change="recalculateDPPoints"
                                                                           class="w-4 h-4 text-blue-500 bg-gray-800 border-gray-600 focus:ring-blue-500">
                                                                    <span class="text-sm font-medium text-gray-300">
                                                                        {{ $option['name'] }} <b class="text-gray-500">({{ $option['value'] }} pts)</b>
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        </div>

                                                    @elseif ($item['type'] === 'number')
                                                        <div class="flex flex-col gap-2 w-full md:w-auto">
                                                            @foreach ($item['options'] as $optIndex => $option)
                                                                <div class="flex items-center gap-3 bg-gray-800 p-2.5 rounded-md border border-gray-700 justify-between">
                                                                    <span class="text-sm text-gray-400 font-medium mr-4">{{ $option['name'] }}</span>
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-xs font-bold text-gray-500">({{ $option['value'] }} pts) ×</span>
                                                                        <input type="number" 
                                                                               min="0" max="20" placeholder="0"
                                                                               class="w-16 bg-gray-900 border border-gray-600 rounded px-2 py-1 text-center font-bold text-gray-200 focus:outline-none focus:border-blue-500"
                                                                               wire:model="scoresDP.{{ $blockIndex }}.{{ $itemIndex }}.{{ $optIndex }}"
                                                                               wire:input="updateScoreDPManual($event.target.value, '{{ $blockIndex }}.{{ $itemIndex }}.{{ $optIndex }}')">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                    @elseif ($item['type'] === 'bonus')
                                                        <label class="flex items-center gap-3 cursor-pointer select-none bg-amber-900/40 border-2 border-amber-600/60 px-4 py-3 rounded-lg hover:bg-amber-800/40 transition-colors">
                                                            <input type="checkbox" 
                                                                   value="{{ $item['value'] }}"
                                                                   wire:model="scoresBonus.{{ $blockIndex }}.{{ $itemIndex }}"
                                                                   wire:change="recalculateDPPoints"
                                                                   class="w-5 h-5 accent-amber-500 rounded">
                                                            <span class="text-sm font-bold text-amber-300">
                                                                Ativar Bônus (+{{ $item['value'] }} pts)
                                                            </span>
                                                        </label>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <hr class="border-gray-800 my-6">
                            @endforeach
                        @else
                            @php
                                $modalitieLevel = $category['question_level'] ?? 'basic';
                                $radioRange = match ($modalitieLevel) {
                                    'basic' => range(1, 20),
                                    'advanced' => range(1, 9),
                                    default => range(1, 9),
                                };
                            @endphp

                            @foreach ($question['assessment'] as $blockIndex => $block)
                                @if (!empty($block['description']))
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold text-blue-300">{{ $block['object'] }}</h3>
                                        <ul class="text-gray-300 list-none">
                                            @foreach ($block['description'] as $index => $desc)
                                                <li class="mb-4">
                                                    <div class="mb-2 text-gray-200 font-medium">{{ $desc }}</div>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach ($radioRange as $i)
                                                            <label class="flex flex-col items-center cursor-pointer select-none">
                                                                <span class="mb-1 text-sm font-semibold text-gray-400">{{ $i }}</span>
                                                                <input type="radio"
                                                                       name="assessment_{{$blockIndex}}_{{$index}}"
                                                                       value="{{ $i }}" 
                                                                       wire:model="scores.{{ $blockIndex }}.{{ $index }}"
                                                                       class="w-4 h-4 text-blue-500 bg-gray-800 border-gray-600 focus:ring-blue-500">
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <hr class="border-gray-800 my-6">
                                @endif
                            @endforeach
                        @endif

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-blue-300 mb-3">Observações gerais e Comentários</h3>
                            <textarea wire:model="comment" rows="4"
                                class="w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg p-3 focus:ring-2 focus:ring-blue-500"
                                placeholder="Digite feedbacks para a equipe..."></textarea>
                            <hr class="border-gray-800 my-6">

                            <div class="text-end">
                                <button wire:click="saveScores"
                                        class="bg-green-600 hover:bg-green-500 text-white px-6 py-2.5 font-bold rounded-lg transition-colors" 
                                        type="button">
                                    Salvar Nota Oficial
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
                            <span class="block font-bold">Dados incompletos para avaliação.</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-gray-900 border border-yellow-900/50 text-yellow-400 px-4 py-3 rounded-xl" role="alert">
                    <span class="block font-bold">Não há equipes disponíveis para avaliação nesta categoria/modalidade.</span>
                </div>
            @endif
        @else
            <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
                <span class="block font-bold">Evento finalizado...</span>
            </div>
        @endif
    @else
        <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
            <span class="block font-bold">Dados incompletos para avaliação.</span>
        </div>
    @endif

    @if ($showSuccessModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/70 z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 w-80 text-center">
                <h2 class="text-lg font-semibold mb-4 text-gray-100">Nota salva com sucesso!</h2>
                <button wire:click="closeSuccessModal" onclick="window.location.reload()"
                        class="px-5 py-2 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg transition-colors">
                    OK
                </button>
            </div>
        </div>
    @endif
</div>

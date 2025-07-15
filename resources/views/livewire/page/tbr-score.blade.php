{{-- @dd($question, $hasAssessment, $filteredTeams) --}}

<div class="p-6">
    @if ($event && $category && $modality)
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h1 class="text-2xl font-semibold text-gray-500">Evento</h1>
            <p class="text-lg font-bold text-gray-800">{{ $event['nome'] ?? '—' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">          
            <!-- Card Categoria -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-500 mb-1">Categoria</h2>
                <p class="text-lg font-bold text-gray-800">{{ $category['label'] ?? strtoupper($category_id) }}</p>
            </div>

            <!-- Card Modalidade -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-500 mb-1">Modalidade</h2>
                <p class="text-lg font-bold text-gray-800">{{ $modality['label'] ?? strtoupper($modality_id) }}</p>
            </div>
        </div>

        @if (!empty($filteredTeams) && $question)
            {{-- Exibe select de equipes e perguntas --}}
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Selecione uma equipe</h2>

                <select wire:model="selectedTeamId" class="w-full border-gray-300 rounded shadow-sm">
                    <option value="">Selecione...</option>
                    @foreach ($filteredTeams as $team)
                        <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Critérios de Avaliação</h2>

                @if ($hasAssessment)
                    @php
                        $modalitieLevel = $category['question'] ?? 'basic';
                        $radioRange = match ($modalitieLevel) {
                            'basic' => range(0, 20),
                            'advanced' => range(1, 9),
                            default => range(1, 9),
                        };
                    @endphp

                    @foreach ($question['assessment'] as $block)
                        @if (!empty($block['description']))
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-blue-700">{{ $block['object'] }}</h3>
                                <ul class="text-gray-700 list-none">
                                    @foreach ($block['description'] as $index => $desc)
                                        <li class="mb-4">
                                            <div class="mb-2 text-gray-800 font-medium">{{ $desc }}</div>
                                            <div class="flex space-x-3">
                                                @foreach ($radioRange as $i)
                                                    <label
                                                        class="flex flex-col items-center cursor-pointer select-none">
                                                        <span
                                                            class="mb-1 text-sm font-semibold text-gray-700">{{ $i }}</span>
                                                        <input type="radio"
                                                            name="assessment_{{ $block['object'] }}_{{ $index }}"
                                                            value="{{ $i }}" class="form-radio"
                                                            wire:model="scores.{{ $block['object'] }}.{{ $index }}">
                                                    </label>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <hr class="border-gray-300 my-6">
                        @endif
                    @endforeach

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-blue-700">Observações</h3>
                        <textarea wire:model="comment" rows="4" class="w-full border-gray-300 rounded shadow-sm p-2"></textarea>

                        <hr class="border-gray-300 my-6">

                        <div class="text-end">
                            <button wire:click="saveScores"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" type="button">
                                Salvar Nota
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                        <span class="block font-bold">Dados incompletos para exibir pontuação.</span>
                    </div>
                @endif
            </div>
        @else
            {{-- Se faltam equipes ou perguntas, mostra a mensagem --}}
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded" role="alert">
                <span class="block font-bold">Não há equipes ou perguntas para avaliação nesta
                    categoria/modalidade.</span>
            </div>
        @endif
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <span class="block font-bold">Dados incompletos para exibir pontuação.</span>
        </div>
    @endif
</div>

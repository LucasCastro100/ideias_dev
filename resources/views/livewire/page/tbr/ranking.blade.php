<div x-data="{ tab: '{{ array_key_first($teamsByCategory) ?? '' }}' }">
    @if ($event)
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('tbr.event-detail', ['event_id' => $event->id]) }}"
                        class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endauth
                <div>
                    <h1 class="text-2xl font-semibold text-gray-100">Pontuação</h1>
                    <p class="text-sm text-gray-400 mt-1">{{ $event->name }}</p>
                </div>
            </div>
        </div>

        @if (empty($teamsByCategory))
            <div class="bg-gray-900 border border-yellow-900/50 text-yellow-400 px-4 py-3 rounded-xl mb-6" role="alert">
                <span class="block font-bold">Nenhuma equipe cadastrada ou pontuada ainda.</span>
            </div>
        @else
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6">
                <div class="mb-4 border-b border-gray-800">
                    <nav class="flex flex-wrap gap-2">
                        @foreach ($teamsByCategory as $categorySlug => $data)
                            <button class="py-2.5 px-5 text-sm font-medium rounded-t-lg focus:outline-none transition"
                                :class="{
                                    'bg-blue-600 text-white': tab === '{{ $categorySlug }}',
                                    'text-gray-400 hover:text-gray-200 hover:bg-gray-800': tab !== '{{ $categorySlug }}'
                                }"
                                @click="tab = '{{ $categorySlug }}'">
                                {{ $data['label'] }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                @foreach ($teamsByCategory as $categorySlug => $data)
                    @php
                        $hasModalities = count($data['modalities_data']) > 0;
                        $topPos = $data['top_positions'] ?? 3;
                        $generalTopPos = $data['general_top_positions'] ?? 3;
                    @endphp

                    <div x-show="tab === '{{ $categorySlug }}'" x-transition>
                        <div class="grid grid-cols-1 md:grid-cols-{{ $hasModalities ? '3' : '1' }} gap-6">
                            @if ($hasModalities)
                                <div class="space-y-6 md:col-span-2">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($data['modalities_data'] as $modality)
                                            @php
                                                $modSlug = $modality['slug'];
                                                $modLabel = $modality['label'];
                                                $teamsModalities = $data['modalities'][$modSlug] ?? [];
                                            @endphp

                                            <div>
                                                <h3 class="text-md font-semibold text-gray-300 mb-3">{{ $modLabel }}</h3>
                                                @php $highlightedIds = $data['modalities_highlighted_ids'][$modSlug] ?? []; @endphp
                                                <div class="overflow-x-auto rounded-lg border border-gray-800">
                                                    <table class="min-w-full">
                                                        <thead>
                                                            <tr class="bg-gray-800">
                                                                <th class="px-4 py-3  text-xs font-semibold text-gray-400 uppercase">Equipe</th>
                                                                <th class="px-4 py-3  text-xs font-semibold text-gray-400 uppercase">Pontos</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-800">
                                                            @foreach ($teamsModalities as $team)
                                                                @php
                                                                    $hlIndex = array_search($team['id'], $highlightedIds);
                                                                    $isHighlighted = $hlIndex !== false;
                                                                @endphp
                                                                <tr class="{{ $isHighlighted ? ($hlIndex === 0 ? 'bg-yellow-900/20' : ($hlIndex === 1 ? 'bg-gray-800/50' : 'bg-orange-900/20')) : 'bg-gray-900' }}">
                                                                    <td class="px-4 py-3 text-sm {{ $isHighlighted ? 'font-bold text-gray-100' : 'text-gray-300' }}">
                                                                        {{ $team['name'] }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm text-right font-semibold text-blue-400">
                                                                        {{ round($team['score']) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="md:col-span-1">
                                <h3 class="text-md font-semibold text-gray-300 mb-3">Pontuação Geral</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-800">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="bg-gray-800">
                                                <th class="px-4 py-3  text-xs font-semibold text-gray-400 uppercase">Equipe</th>
                                                <th class="px-4 py-3  text-xs font-semibold text-gray-400 uppercase">Pontos</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-800">
                                            @foreach ($data['total'] as $index => $team)
                                                <tr class="{{ $index < $generalTopPos ? ($index === 0 ? 'bg-yellow-900/20' : ($index === 1 ? 'bg-gray-800/50' : 'bg-orange-900/20')) : 'bg-gray-900' }}">
                                                    <td class="px-4 py-3 text-sm {{ $index < $generalTopPos ? 'font-bold text-gray-100' : 'text-gray-300' }}">
                                                        {{ $team['name'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-400">
                                                        {{ round($team['total']) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl mb-6" role="alert">
            <span class="block font-bold">Evento não encontrado.</span>
        </div>
    @endif
</div>
<div class="p-6" x-data="{ tab: '{{ array_key_first($teamsByCategory) ?? '' }}' }">
    @if ($event)
        {{-- Cabeçalho evento --}}
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h1 class="text-3xl font-semibold text-gray-500 mb-1">Pontuação</h1>
            <p class="text-lg font-bold text-gray-800">{{ $event['nome'] }}</p>
        </div>

        @if (empty($teamsByCategory))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6" role="alert">
                <span class="block font-bold">Nenhuma equipe cadastrada ou pontuada ainda.</span>
            </div>
        @else
            {{-- TABS POR CATEGORIA --}}
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="mb-4 border-b border-gray-200">
                    <nav class="flex flex-wrap gap-2">
                        @foreach ($teamsByCategory as $categorySlug => $data)
                            <button class="py-2 px-4 text-sm font-medium rounded-t-lg focus:outline-none"
                                :class="{
                                    'bg-blue-500 text-white': tab === '{{ $categorySlug }}',
                                    'text-gray-600 hover:bg-gray-100': tab !== '{{ $categorySlug }}'
                                }"
                                @click="tab = '{{ $categorySlug }}'">
                                {{ $data['label'] }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- CONTEÚDO DE CADA CATEGORIA --}}
                @foreach ($teamsByCategory as $categorySlug => $data)
                    @php
                        $hasModalities = count($data['modalities_data']) > 0;
                        $totalColSpan = $hasModalities ? 1 : 3;
                        $topPos = $data['top_positions'] ?? 3;
                        $generalTopPos = $data['general_top_positions'] ?? 3;
                        $borderClass = $hasModalities ? 'border-l border-gray-300 pl-6' : '';
                    @endphp

                    <div x-show="tab === '{{ $categorySlug }}'" x-transition>
                        <div class="grid grid-cols-1 md:grid-cols-{{ $hasModalities ? '3' : '1' }} gap-6">
                            @if ($hasModalities)
                                {{-- Modalidades --}}
                                <div class="space-y-6 md:col-span-2">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($data['modalities_data'] as $modality)
                                            @php
                                                $modSlug = $modality['slug'];
                                                $modLabel = $modality['label'];
                                                $teamsModalities = $data['modalities'][$modSlug] ?? [];
                                            @endphp

                                            <div>
                                                <h3 class="text-md font-semibold text-gray-700 mb-2">{{ $modLabel }}
                                                </h3>
                                                <table class="min-w-full table-auto border border-gray-200">
                                                    <thead class="bg-gray-100">
                                                        <tr>
                                                            <th class="px-4 py-2 border">Equipe</th>
                                                            <th class="px-4 py-2 border">Pontos</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($teamsModalities as $index => $team)
                                                            <tr @if ($index < $topPos) @if ($index === 0) class="bg-yellow-100 font-bold"
                                                                    @elseif ($index === 1) class="bg-gray-200 font-semibold"
                                                                    @elseif ($index === 2) class="bg-orange-100 font-medium" @endif
                                                                @endif
                                                                >
                                                                <td class="px-4 py-2 border">{{ $team['name'] }}</td>
                                                                <td class="px-4 py-2 border text-blue-600">
                                                                    {{ number_format($team['score'], 2) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Total Geral --}}
                            <div class="{{ $borderClass }} md:col-span-{{ $totalColSpan }}">
                                <h3 class="text-md font-semibold text-gray-700 mb-2">Pontuação Geral</h3>
                                <table class="min-w-full table-auto border border-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 border">Equipe</th>
                                            <th class="px-4 py-2 border">Pontos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['total'] as $index => $team)
                                            <tr @if ($index < $generalTopPos) @if ($index === 0) class="bg-yellow-200 font-bold"
                                                    @elseif ($index === 1) class="bg-gray-200 font-semibold"
                                                    @elseif ($index === 2) class="bg-orange-100 font-medium" @endif
                                                @endif
                                                >
                                                <td class="px-4 py-2 border">{{ $team['name'] }}</td>
                                                <td class="px-4 py-2 border text-green-600">
                                                    {{ number_format($team['total'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                @endforeach

                @if (!empty($event) && isset($event['id']))
                    <div class="flex justify-end mt-6">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 font-semibold flex items-center gap-2">
                                Exportar (Vencedores)
                                <i :class="{ 'rotate-180': open }"
                                    class="fas fa-chevron-down transition-transform duration-200"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false"
                                class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded shadow-lg z-50"
                                x-transition>
                                <a href="{{ route('tbr.ranking.pptx', ['event_id' => $event['id']]) }}"
                                    class="block px-4 py-2 text-left text-gray-700 hover:bg-orange-100 hover:text-orange-700">
                                    PowerPoint
                                </a>

                                <a href="{{ route('tbr.ranking.pdf', ['event_id' => $event['id']]) }}"
                                    class="block px-4 py-2 text-left text-gray-700 hover:bg-red-100 hover:text-red-700">
                                    PDF
                                </a>

                                <a wire:navigate href="{{ route('tbr.slide', ['event_id' => $event['id']]) }}"
                                    class="block px-4 py-2 text-left text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                                    HTML
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block font-bold">Evento não encontrado.</span>
        </div>
    @endif
</div>

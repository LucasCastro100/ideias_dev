<div>
    @if ($categoriesWithModalities)
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('tbr.event-detail', ['event_id' => $categoriesWithModalities['event_id']]) }}"
                        class="w-9 h-9 flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endauth
                <div>
                    <h1 class="text-2xl font-semibold text-gray-100">Links</h1>
                    <p class="text-sm text-gray-400 mt-1">{{ $categoriesWithModalities['event'] }}</p>
                </div>
            </div>
        </div>

        @if ($status == 0)
            <div class="grid grid-cols-1 gap-6 mb-6">
                @foreach ($categoriesWithModalities['links'] as $link)
                    <div class="bg-gray-900 border border-gray-800 p-6 rounded-xl">
                        <h2 class="text-xl font-semibold text-gray-300 mb-4">{{ $link['category_name'] }}</h2>

                        <ul class="grid grid-cols-1 gap-4">
                            @foreach ($link['modalities'] as $modality)
                                <li class="flex flex-col sm:flex-row sm:items-center gap-2 p-4 bg-gray-800/50 rounded-lg border border-gray-800">
                                    <span class="text-gray-300 font-medium whitespace-nowrap">{{ $modality['name'] }}:</span>
                                    <a href="{{ route('tbr.score', [
                                        'event_id' => $categoriesWithModalities['event_id'],
                                        'category_id' => $link['category_id'],
                                        'modality_id' => $modality['id'],
                                    ]) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="text-blue-400 hover:text-blue-300 underline break-all text-sm sm:text-base">
                                        {{ route('tbr.score', [
                                            'event_id' => $categoriesWithModalities['event_id'],
                                            'category_id' => $link['category_id'],
                                            'modality_id' => $modality['id'],
                                        ]) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-900 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl" role="alert">
                <span class="block font-bold">Evento finalizado...</span>
            </div>
        @endif
    @else
        <div class="bg-gray-900 border border-yellow-900/50 text-yellow-400 px-4 py-3 rounded-xl" role="alert">
            <span class="block font-bold">Evento não existe.</span>
        </div>
    @endif
</div>
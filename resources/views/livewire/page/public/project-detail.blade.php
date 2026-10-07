<div class="max-w-3xl mx-auto">
    <a href="{{ route('projetos.index') }}" class="text-sm text-gray-500 hover:text-gray-300 transition inline-flex items-center gap-1.5 mb-6">
        <i class="fas fa-arrow-left text-xs"></i> Voltar para projetos
    </a>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-xl bg-blue-600/10 flex items-center justify-center">
                <i class="fas {{ $project->icon }} text-2xl text-blue-400"></i>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wide text-gray-500">{{ $project->category }}</span>
                <h1 class="text-2xl font-bold text-gray-100">{{ $project->name }}</h1>
            </div>
        </div>

        <p class="text-gray-300 leading-relaxed">{{ $project->long_description }}</p>

        @if (!empty($project->workflow))
            <div class="mt-8">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-3">Como funciona</h2>
                <ol class="space-y-3">
                    @foreach ($project->workflow as $i => $step)
                        <li class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600/20 text-blue-400 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                            <span class="text-gray-300 text-sm">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

        <div class="mt-8 pt-6 border-t border-gray-800">
            @if ($accessRouteName)
                <a href="{{ route($accessRouteName) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Acessar sistema <i class="fas fa-arrow-right text-xs"></i>
                </a>
                <span class="text-xs text-gray-500 ml-2">Requer login com uma conta vinculada a este sistema.</span>
            @else
                <span class="text-sm text-gray-500">Sistema em preparação.</span>
            @endif
        </div>
    </div>

    @if ($related->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-3">Projetos relacionados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach ($related as $r)
                    <a href="{{ route('projetos.show', $r->slug) }}"
                        class="bg-gray-900 border border-gray-800 rounded-lg p-4 hover:border-blue-600 transition">
                        <i class="fas {{ $r->icon }} text-blue-400 mb-2"></i>
                        <p class="text-sm font-medium text-gray-200">{{ $r->name }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

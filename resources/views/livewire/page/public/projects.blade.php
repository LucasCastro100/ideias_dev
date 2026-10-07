<div class="max-w-6xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-100">Projetos</h1>
        <p class="text-gray-400 mt-2">Sistemas desenvolvidos e disponíveis dentro da plataforma Ideias Dev.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar projeto..."
            class="flex-1 bg-gray-900 border border-gray-800 text-gray-200 rounded-lg px-4 py-2.5 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <select wire:model.live="category"
            class="bg-gray-900 border border-gray-800 text-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Todas as categorias</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($projects as $project)
            <a href="{{ route('projetos.show', $project->slug) }}"
                class="group bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-blue-600 transition flex flex-col">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-600/10 flex items-center justify-center">
                        <i class="fas {{ $project->icon }} text-blue-400"></i>
                    </div>
                    <span class="text-xs uppercase tracking-wide text-gray-500">{{ $project->category }}</span>
                </div>
                <h2 class="text-lg font-semibold text-gray-100 group-hover:text-blue-400 transition">{{ $project->name }}</h2>
                <p class="text-sm text-gray-400 mt-2 flex-1">{{ $project->short_description }}</p>
                <span class="text-sm text-blue-400 mt-4 inline-flex items-center gap-1.5">
                    Ver detalhes <i class="fas fa-arrow-right text-xs"></i>
                </span>
            </a>
        @empty
            <div class="col-span-full text-center text-gray-500 py-12">Nenhum projeto encontrado.</div>
        @endforelse
    </div>
</div>

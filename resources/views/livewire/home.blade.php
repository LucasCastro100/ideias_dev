<div>
    <x-slot name="header">
        <div class="w-full text-center">
            <h2 class="font-semibold text-4xl text-gray-100 leading-tight inline-block">
                {{ __('Bem vindo ao Ideias.dev') }}
            </h2>
        </div>
    </x-slot>

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 opacity-90"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-900/20 via-transparent to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-24 sm:py-32 lg:py-40">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-100 leading-tight mb-6">
                    Transforme Suas Ideias em<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Projetos Digitais de Sucesso</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-400 max-w-3xl mx-auto mb-10 leading-relaxed">
                    Desde 2024 transformando ideias em soluções digitais que impulsionam o sucesso e a eficiência do seu negócio.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#servicos"
                        class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-600/25">
                        Ver Serviços
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                    <a href="#contato"
                        class="inline-flex items-center justify-center px-8 py-4 bg-gray-800 hover:bg-gray-700 text-gray-200 font-semibold rounded-xl border border-gray-700 transition-all duration-200">
                        Fale Conosco
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- SERVIÇOS --}}
    <section id="servicos" class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-100 mb-4">Nossos Serviços</h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    Soluções completas para sua presença digital, do planejamento à manutenção.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 hover:border-gray-700 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-600/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-code text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-100 mb-3">Desenvolvimento de Sites</h3>
                    <p class="text-gray-400 leading-relaxed">Criação de sites e sistemas responsivos, com performance e SEO em foco.</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 hover:border-gray-700 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-green-600/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-robot text-green-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-100 mb-3">Automações com Python</h3>
                    <p class="text-gray-400 leading-relaxed">Automatize processos e economize tempo com soluções sob medida.</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 hover:border-gray-700 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-yellow-600/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fab fa-google text-yellow-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-100 mb-3">Google Ads</h3>
                    <p class="text-gray-400 leading-relaxed">Campanhas otimizadas para gerar mais alcance, tráfego e conversões.</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 hover:border-gray-700 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-red-600/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tools text-red-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-100 mb-3">Suporte Técnico</h3>
                    <p class="text-gray-400 leading-relaxed">Manutenção contínua para garantir estabilidade e evolução do seu sistema.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- PROJETOS --}}
    @if ($projects->isNotEmpty())
        <section id="projetos" class="py-16 sm:py-24">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-100 mb-4">Projetos</h2>
                    <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                        Sistemas desenvolvidos e disponíveis dentro da plataforma Ideias Dev.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($projects as $project)
                        <a href="{{ route('projetos.show', $project->slug) }}"
                            class="group bg-gray-900 border border-gray-800 rounded-2xl p-6 hover:border-blue-600 transition-all duration-300 flex flex-col">
                            <div class="w-12 h-12 rounded-xl bg-blue-600/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas {{ $project->icon }} text-blue-400"></i>
                            </div>
                            <span class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $project->category }}</span>
                            <h3 class="text-lg font-bold text-gray-100 group-hover:text-blue-400 transition">{{ $project->name }}</h3>
                            <p class="text-sm text-gray-400 mt-2 leading-relaxed flex-1">{{ $project->short_description }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('projetos.index') }}"
                        class="inline-flex items-center justify-center px-8 py-3.5 bg-gray-800 hover:bg-gray-700 text-gray-200 font-semibold rounded-xl border border-gray-700 transition-all duration-200">
                        Ver mais projetos
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- SOBRE --}}
    <section class="py-16 sm:py-24 bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-100 mb-6">Como Trabalhamos</h2>
                <p class="text-gray-400 text-lg leading-relaxed mb-8">
                    Na Ideias.dev, cada projeto é único. Acreditamos que a personalização faz toda a diferença e,
                    por isso, cuidamos de cada etapa com atenção aos mínimos detalhes — do planejamento inicial à
                    implementação final, passando por testes rigorosos e acompanhamento pós-lançamento.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-12">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-400 mb-2">01</div>
                        <h4 class="text-gray-200 font-semibold mb-2">Planejamento</h4>
                        <p class="text-gray-500 text-sm">Entendemos suas necessidades e definimos a melhor estratégia.</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-400 mb-2">02</div>
                        <h4 class="text-gray-200 font-semibold mb-2">Desenvolvimento</h4>
                        <p class="text-gray-500 text-sm">Criamos a solução com as melhores tecnologias do mercado.</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-400 mb-2">03</div>
                        <h4 class="text-gray-200 font-semibold mb-2">Entrega & Suporte</h4>
                        <p class="text-gray-500 text-sm">Entregamos e acompanhamos para garantir seu sucesso.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTATO --}}
    <section id="contato" class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-100 mb-4">Entre em Contato</h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    Tem um projeto em mente? Preencha o formulário e vamos conversar.
                </p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 sm:p-10">
                    <form wire:submit="submit_form" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-label for="name" value="Nome" />
                                <x-input id="name" type="text" wire:model.live="name" class="w-full mt-1" />
                                <x-input-error for="name" class="mt-1" />
                            </div>

                            <div>
                                <x-label for="email" value="Email" />
                                <x-input id="email" type="email" wire:model.live="email" class="w-full mt-1" />
                                <x-input-error for="email" class="mt-1" />
                            </div>

                            <div>
                                <x-label for="phone" value="Telefone" />
                                <x-input id="phone" type="text" wire:model.live="phone" class="w-full mt-1" />
                                <x-input-error for="phone" class="mt-1" />
                            </div>

                            <div>
                                <x-label for="domain" value="Domínio (SIM ou NÃO)" />
                                <x-input id="domain" type="text" wire:model.live="domain" class="w-full mt-1" />
                                <x-input-error for="domain" class="mt-1" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-label for="urlDomain" value="URL" />
                                <x-input id="urlDomain" type="text" wire:model.live="urlDomain" class="w-full mt-1" />
                                <x-input-error for="urlDomain" class="mt-1" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-label for="desc" value="Descrição" />
                                <x-text-area id="desc" cols="10" rows="5" wire:model.live="desc" class="w-full mt-1"></x-text-area>
                                <x-input-error for="desc" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-600/25">
                                Enviar Mensagem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-gray-800 bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="text-center">
                <p class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Ideias.dev. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>
</div>
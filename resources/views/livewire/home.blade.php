<div>
    <x-slot name="header">
        <div class="w-full text-center">
            <h2 class="font-semibold text-4xl text-black dark:text-white leading-tight inline-block">
                {{ __('Bem vindo ao Ideias.dev') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"> --}}

            <div class="grid gap-4 grid-cols-1">
                {{-- DESCRICAO --}}
                <div class="p-4">
                    <h2 class="font-semibold text-black mb-4 text-center text-2xl">Transforme Suas Ideias em Projetos
                        Digitais de Sucesso</h2>

                    <p class="font-semibold text-black text-lg mb-4">
                        Transformar ideias em soluções digitais é o que me move. Desde 2024 nosso objetivo é
                        entregar
                        projetos que não apenas atendem às expectativas, mas também impulsionam o sucesso e a
                        eficiência dos seu negócio.
                    </p>

                    <p class="font-semibold text-black text-lg mb-4">
                        Na Ideias.dev, cada projeto é único. Acredito que a personalização faz toda a diferença e,
                        por isso, cuido de cada etapa com atenção aos mínimos detalhes do planejamento inicial à
                        implementação final, passando por testes rigorosos e acompanhamento pós-lançamento.
                    </p>

                    <p class="font-semibold text-black text-lg">
                        Além do desenvolvimento, ofereço um portfólio de serviços para ampliar sua presença digital:
                    </p>
                </div>

                {{-- TIPO SERVICO --}}
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Desenvolvimento de Sites -->
                        <div
                            class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex flex-col items-center">
                            <i class="fas fa-code text-blue-600 text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold text-blue-600 mb-4 text-center">Desenvolvimento de Sites
                            </h3>
                            <p class="text-gray-600 text-center">Criação de sites e sistemas responsivos, com
                                performance e SEO em foco.</p>
                        </div>

                        <!-- Automações com Python -->
                        <div
                            class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex flex-col items-center">
                            <i class="fas fa-robot text-green-600 text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold text-green-600 mb-4 text-center">Automações com Python</h3>
                            <p class="text-gray-600 text-center">Automatize processos e economize tempo com soluções
                                sob medida.</p>
                        </div>

                        <!-- Google Ads -->
                        <div
                            class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex flex-col items-center">
                            <i class="fab fa-google text-yellow-600 text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold text-yellow-600 mb-4 text-center">Google Ads</h3>
                            <p class="text-gray-600 text-center">Campanhas otimizadas para gerar mais alcance,
                                tráfego e conversões.</p>
                        </div>

                        <!-- Suporte Técnico -->
                        <div
                            class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition flex flex-col items-center">
                            <i class="fas fa-tools text-red-600 text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold text-red-600 mb-4 text-center">Suporte Técnico</h3>
                            <p class="text-gray-600 text-center">Manutenção contínua para garantir estabilidade e
                                evolução do seu sistema.</p>
                        </div>
                    </div>
                </div>

                {{-- FORM --}}
                <div class="p-4">
                    <h2 class="font-semibold text-black mb-4 text-center text-2xl">Contato</h2>

                    <form wire:submit="submit_form" class="w-full">
                        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            {{-- Nome --}}
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <x-label for="name" value="Nome" />
                                <x-input id="name" type="text" wire:model.live="name" class="w-full" />
                                <x-input-error for="name" class="mt-1" />
                            </div>

                            {{-- Email --}}
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <x-label for="email" value="Email" />
                                <x-input id="email" type="email" wire:model.live="email" class="w-full" />
                                <x-input-error for="email" class="mt-1" />
                            </div>

                            {{-- Telefone --}}
                            <div class="col-span-1 lg:col-span-2">
                                <x-label for="phone" value="Telefone" />
                                <x-input id="phone" type="text" wire:model.live="phone" class="w-full" />
                                <x-input-error for="phone" class="mt-1" />
                            </div>

                            {{-- Domínio --}}
                            <div class="col-span-1 lg:col-span-2">
                                <x-label for="domain" value="Domínio (SIM ou NÃO)" />
                                <x-input id="domain" type="text" wire:model.live="domain" class="w-full" />
                                <x-input-error for="domain" class="mt-1" />
                            </div>

                            {{-- URL --}}
                            <div class="col-span-1 md:col-span-2">
                                <x-label for="url" value="URL" />
                                <x-input id="url" type="text" wire:model.live="url" class="w-full" />
                                <x-input-error for="url" class="mt-1" />
                            </div>

                            {{-- Descrição --}}
                            <div class="col-span-1 md:col-span-4 lg:col-span-6">
                                <x-label for="desc" value="Descrição" />
                                <x-text-area id="desc" cols="10" rows="5" wire:model.live="desc"
                                    class="w-full"></x-text-area>
                                <x-input-error for="desc" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

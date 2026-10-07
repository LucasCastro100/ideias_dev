<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Meta Tags -->
    <title>Título da Página</title>
    <meta name="description" content="Descrição da página aqui">
    <meta name="keywords" content="música, produção musical, intros, beats, artista, cantor">
    <meta name="author" content="Seu Nome ou Marca">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Título para redes sociais" />
    <meta property="og:description" content="Descrição para redes sociais" />
    <meta property="og:image" content="URL_DA_IMAGEM" />
    <meta property="og:url" content="https://seudominio.com" />
    <meta property="og:type" content="website" />

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Título para Twitter" />
    <meta name="twitter:description" content="Descrição para Twitter" />
    <meta name="twitter:image" content="URL_DA_IMAGEM" />
    <meta name="twitter:url" content="https://seudominio.com" />

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  

    <!-- Ícones -->

    <style>
        #site-header,
        #site-footer {
            display: none
        }

        .elementor embed, .elementor iframe, .elementor object, .elementor video{
            border: auto !important;
        }
    </style>

     <!-- Tailwind CSS -->
     <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased">

    <div class="bg-black text-white">

        <section style="background-image: url('{{ asset('storage/rc-studio/fundo-dobra-1.webp') }}')"
            class="relative bg-auto bg-center bg-no-repeat" role="region" aria-labelledby="video-heading">

            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">

                <header class="text-center">
                    <h1 id="video-heading" class="font-ethnocentric text-2xl lg:text-4xl mb-2" tabindex="0">
                        Orquestre o Show Perfeito, Faça as suas produções impressionarem de verdade
                    </h1>

                    <h2 class="font-bebas text-xl lg:text-3xl" tabindex="0">
                        O Shows Pro é o pack de samples e efeitos que vai mudar sua maneira de produzir! Eleve o nível
                        da
                        sua produção do dia pra noite!
                    </h2>
                </header>

                <div class="relative w-full" style="padding-top: 56.25%;">
                    <iframe class="absolute top-0 left-0 w-full h-full border-4 border-blue-600 rounded-lg"
                    style="border-width: 4px; border-radius: 0.5rem; --tw-border-opacity: 1;
                    border-color: rgb(37 99 235 / var(--tw-border-opacity, 1));"
                        src="https://www.youtube.com/embed/k18wZeXXL1c"
                        title="Vídeo promocional do Shows Pro explicando os benefícios do pack"
                        aria-label="Vídeo explicativo do produto Shows Pro" frameborder="0" loading="lazy"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>
                </div>

                <button
                    class="px-8 py-3 font-bold text-white rounded
               bg-gradient-to-r from-[#8b0000] to-[#b22222]
               shadow-[0_0_20px_#b22222]
               transform transition-transform duration-300
               hover:scale-105 hover:brightness-110
               focus:outline-none focus:ring-4 focus:ring-red-700 focus:ring-opacity-60"
                    aria-label="Botão para garantir o produto Shows Pro">
                    GARANTIR O SHOWS PRO AGORA
                </button>


                <div class="w-full flex justify-center">
                    <img src="{{ asset('storage/rc-studio/pagamento.webp') }}"
                        alt="Meios de pagamento aceitos para adquirir o Shows Pro"
                        title="Formas de pagamento disponíveis" class="max-w-full rounded shadow-md" loading="lazy"
                        decoding="async" fetchpriority="low" />
                </div>
            </div>
        </section>

        <section style="background-image: url('{{ asset('storage/rc-studio/fundo-dobra-2.webp') }}')"
            class="relative bg-auto bg-center bg-no-repeat" role="region" aria-labelledby="descricao-pack">

            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">              

                <h2 id="descricao-pack" class="font-bebas text-xl lg:text-3xl" tabindex="0">
                    Você é produtor, músico ou cantor e sabe:
                </h2>

                <ul class="text-lg md:text-xl text-gray-200 mb-8 space-y-4"
                    aria-label="Lista de dores e desafios comuns">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-music text-white-400 mt-1" aria-hidden="true"></i>
                        <span>
                            A <span class="text-white font-bold">abertura decide</span> se a plateia vai arrepiar… ou
                            abrir
                            o Instagram.
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-music text-white-400 mt-1" aria-hidden="true"></i>
                        <span>
                            Sem um impacto inicial, o show inteiro <span class="text-white font-bold">luta em
                                ladeira.</span>
                        </span>
                    </li>
                </ul>

                <!-- Alerta de dor -->
                <div class="bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-lg font-semibold mb-8 w-full"
                    role="alert" aria-label="Mensagem de alerta sobre desafios na produção">
                    <p class="mb-2" tabindex="0">Chega de não saber por onde começar.</p>
                    <p class="mb-2" tabindex="0">Chega de intros e samples genéricos copiados do YouTube.</p>
                    <p tabindex="0">
                        Chega de passar horas mixando e ainda assim sempre <span class="italic">falta algo</span>.
                    </p>
                </div>

                <!-- Dor reforçada -->
                <p class="text-xl md:text-2xl text-gray-300 mb-10 text-center" tabindex="0">
                    Muitas vezes você terá <span class="text-white font-bold">apenas segundos</span> para provar que não
                    veio brincar!
                </p>

                <button
                    class="px-8 py-3 font-bold text-white rounded
                       bg-gradient-to-r from-[#8b0000] to-[#b22222]
                       shadow-[0_0_20px_#b22222]
                       transform transition-transform duration-300
                       hover:scale-105 hover:brightness-110
                       focus:outline-none focus:ring-4 focus:ring-red-700 focus:ring-opacity-60"
                    aria-label="Botão para garantir o produto Shows Pro">
                    GARANTIR O SHOWS PRO AGORA
                </button>

            </div>
        </section>

        <section style="background-image: url('{{ asset('storage/rc-studio/fundo-dobra-3.webp') }}')"
            class="relative bg-auto bg-center bg-no-repeat" role="region" aria-labelledby="cards-pack">

            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">
                

                <h2 id="cards-pack" class="font-bebas text-xl lg:text-3xl" tabindex="0">
                    Tudo que você precisa está dentro do Pack 🎶SHOWS PRO!🎶
                </h2>

                <ul class="grid grid-cols-1 gap-6" aria-label="Componentes incluídos no pack Shows Pro">

                    <!-- Card 1 -->
                    <li>
                        <article
                            class="bg-gray-900 border border-white rounded-2xl shadow-md p-4 grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                            <img src="/storage/rc-studio/card-samples.webp"
                                alt="Imagem representando os samples incluídos no pack"
                                title="Samples musicais exclusivos" class="w-full h-48 object-cover rounded md:h-full"
                                loading="lazy" decoding="async" fetchpriority="low" />

                            <div class="md:col-span-2 text-left">
                                <h3 id="card1-title" class="text-lg font-semibold mb-2" tabindex="0">Samples
                                    Musicais</h3>
                                <p class="text-sm" tabindex="0">Uma seleção de samples prontos para dar identidade
                                    ao seu
                                    show.</p>
                            </div>
                        </article>
                    </li>

                    <!-- Card 2 -->
                    <li>
                        <article
                            class="bg-gray-900 border border-white rounded-2xl shadow-md p-4 grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                            <div class="md:col-span-2 text-left md:order-1">
                                <h3 id="card2-title" class="text-lg font-semibold mb-2" tabindex="0">Efeitos
                                    Sonoros (FX)
                                </h3>
                                <p class="text-sm" tabindex="0">FX de impacto para criar momentos memoráveis no seu
                                    set.
                                </p>
                            </div>

                            <img src="/storage/rc-studio/card-fx.webp"
                                alt="Imagem ilustrando efeitos sonoros disponíveis"
                                title="Efeitos sonoros impactantes"
                                class="w-full h-48 object-cover rounded md:h-full md:order-2" loading="lazy"
                                decoding="async" fetchpriority="low" />
                        </article>
                    </li>

                    <!-- Card 3 -->
                    <li>
                        <article
                            class="bg-gray-900 border border-white rounded-2xl shadow-md p-4 grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                            <img src="/storage/rc-studio/card-loops.webp"
                                alt="Imagem representando loops incluídos no pack" title="Loops prontos para usar"
                                class="w-full h-48 object-cover rounded md:h-full" loading="lazy" decoding="async"
                                fetchpriority="low" />

                            <div class="md:col-span-2 text-left">
                                <h3 id="card3-title" class="text-lg font-semibold mb-2" tabindex="0">Loops
                                    Exclusivos
                                </h3>
                                <p class="text-sm" tabindex="0">Crie atmosferas envolventes com loops de alta
                                    qualidade.
                                </p>
                            </div>
                        </article>
                    </li>
                </ul>

                <button
                    class="px-8 py-3 font-bold text-white rounded
               bg-gradient-to-r from-[#8b0000] to-[#b22222]
               shadow-[0_0_20px_#b22222]
               transform transition-transform duration-300
               hover:scale-105 hover:brightness-110
               focus:outline-none focus:ring-4 focus:ring-red-700 focus:ring-opacity-60"
                    aria-label="Botão para garantir o produto Shows Pro">
                    GARANTIR O SHOWS PRO AGORA
                </button>

            </div>
        </section>

        <section class="relative" role="region" aria-labelledby="falando-pack">
            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">
                

                <h2 id="falando-pack" class="text-2xl font-semibold" tabindex="0">Lorem ipsum</h2>

                <p class="text-base leading-relaxed" tabindex="0">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua.
                </p>

                <div class="grid gap-6 p-6">
                    <!-- Card 1 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 bg-gray-900 border border-gray-700 rounded-lg p-6 items-center gap-6"
                        role="group" aria-labelledby="card1-pack-title">
                        <div>
                            <h3 id="card1-pack-title" class="text-red-500 text-xl font-bold mb-3" tabindex="0">
                                Lorem
                                ipsum</h3>
                            <ul class="list-disc list-inside text-green-400 text-sm mb-4 space-y-1">
                                <li tabindex="0">Lorem ipsum dolor sit amet</li>
                                <li tabindex="0">Consectetur adipiscing elit</li>
                                <li tabindex="0">Donec vitae sapien ut libero</li>
                                <li tabindex="0">Curabitur ullamcorper ultricies nisi</li>
                            </ul>
                            <p class="text-sm" tabindex="0">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse
                                lectus
                                tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.
                            </p>
                        </div>
                        <div class="flex justify-center" aria-hidden="true"></div>
                    </div>

                    <!-- Card 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 bg-gray-900 border border-gray-700 rounded-lg p-6 items-center gap-6"
                        role="group" aria-labelledby="card2-pack-title">
                        <div>
                            <h3 id="card2-pack-title" class="text-orange-400 text-xl font-bold mb-3" tabindex="0">
                                Lorem
                                ipsum</h3>
                            <p class="text-sm mb-4" tabindex="0">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce dapibus, tellus ac cursus
                                commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
                            </p>
                            <p class="text-sm" tabindex="0">
                                Sed posuere consectetur est at lobortis. Aenean lacinia bibendum nulla sed consectetur.
                            </p>
                        </div>
                        <div class="flex justify-center" aria-hidden="true"></div>
                    </div>

                    <!-- Card 3 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 bg-gray-900 border border-gray-700 rounded-lg p-6 items-center gap-6"
                        role="group" aria-labelledby="card3-pack-title">
                        <div>
                            <h3 id="card3-pack-title" class="text-pink-500 text-xl font-bold mb-3" tabindex="0">
                                Lorem
                                ipsum</h3>
                            <p class="text-sm mb-4" tabindex="0">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sagittis lacus vel
                                augue
                                laoreet rutrum faucibus dolor auctor.
                            </p>
                            <p class="text-sm" tabindex="0">
                                Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.
                            </p>
                        </div>
                        <div class="flex justify-center" aria-hidden="true"></div>
                    </div>
                </div>

                <button
                    class="px-8 py-3 font-bold text-white rounded
               bg-gradient-to-r from-[#8b0000] to-[#b22222]
               shadow-[0_0_20px_#b22222]
               transform transition-transform duration-300
               hover:scale-105 hover:brightness-110
               focus:outline-none focus:ring-4 focus:ring-red-700 focus:ring-opacity-60"
                    aria-label="Botão para garantir o produto Shows Pro">
                    GARANTIR O SHOWS PRO AGORA
                </button>


                <!-- Comparativo final -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6" role="group"
                    aria-labelledby="comparativo-title">
                    <h3 id="comparativo-title" class="sr-only">Comparativo entre o que você evita e o que você ganha
                    </h3>

                    <!-- Coluna esquerda -->
                    <div class="border border-red-700 rounded-lg p-6 bg-gray-900" role="group"
                        aria-label="O que você evita">
                        <h4 class="text-white text-lg font-semibold mb-4" tabindex="0">Sem o pack</h4>
                        <ul class="space-y-3 text-sm" role="list">
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-red-500 text-xl" aria-hidden="true">❌</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-red-500 text-xl" aria-hidden="true">❌</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-red-500 text-xl" aria-hidden="true">❌</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-red-500 text-xl" aria-hidden="true">❌</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                        </ul>
                    </div>

                    <!-- Coluna direita -->
                    <div class="border border-green-600 rounded-lg p-6 bg-gray-900" role="group"
                        aria-label="O que você ganha com o pack">
                        <h4 class="text-white text-lg font-semibold mb-4" tabindex="0">Com o pack Shows Pro</h4>
                        <ul class="space-y-3 text-sm" role="list">
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-green-400 text-xl" aria-hidden="true">✅</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-green-400 text-xl" aria-hidden="true">✅</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-green-400 text-xl" aria-hidden="true">✅</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                            <li class="flex items-start gap-2" tabindex="0">
                                <span class="text-green-400 text-xl" aria-hidden="true">✅</span> Lorem ipsum dolor sit
                                amet.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative" role="region" aria-labelledby="valores">
            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">
                

                <h2 id="valores" class="text-2xl font-semibold">Lorem ipsum dolor sit amet</h2>
                <h3 class="text-xl font-semibold" id="subtitulo-valores">Lorem ipsum consectetur</h3>

                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6"
                    aria-label="Planos e valores disponíveis">

                    <!-- Oferta Básica -->
                    <li class="flex flex-col">
                        <article
                            class="bg-gray-900 border border-gray-700 p-6 rounded-lg flex flex-col text-center shadow-lg h-full">
                            <h4 id="oferta-basica" class="text-2xl font-bold mb-2">Lorem ipsum</h4>
                            <ul class="text-sm mb-4 space-y-1">
                                <li>✅ Lorem ipsum pack <span class="line-through text-red-500">R$00</span></li>
                                <li>✅ Lorem ipsum presets <span class="line-through text-red-500">R$00</span></li>
                            </ul>
                            <div class="mt-auto flex flex-col items-center" aria-label="Preço da oferta básica">
                                <p class="text-xs text-gray-400 mb-2">De <span
                                        class="line-through text-red-500">R$00</span>
                                    por apenas:</p>
                                <p class="text-3xl font-bold text-green-500 mb-4">R$00</p>
                                <button
                                    class="bg-white text-black font-bold px-4 py-2 rounded hover:bg-gray-300 transition"
                                    aria-label="Selecionar oferta básica">
                                    EU QUERO A OFERTA BÁSICA
                                </button>
                                <p class="text-xs mt-2 text-gray-400">ou 12x de <strong>R$00</strong></p>
                            </div>
                        </article>
                    </li>

                    <!-- Oferta Advanced -->
                    <li class="flex flex-col">
                        <article
                            class="bg-gray-900 border border-gray-700 p-6 rounded-lg flex flex-col text-center shadow-lg h-full">
                            <h4 id="oferta-advanced" class="text-2xl font-bold mb-2">Lorem ipsum advanced</h4>
                            <ul class="text-sm mb-4 space-y-1">
                                <li>✅ Lorem ipsum pack <span class="line-through text-red-500">R$00</span></li>
                                <li>✅ Lorem ipsum presets <span class="line-through text-red-500">R$00</span></li>
                                <li>✅ Lorem ipsum aulas <span class="line-through text-red-500">R$00</span></li>
                                <li>✅ Lorem ipsum networking <span class="text-red-500 italic">inacreditável</span>
                                </li>
                                <li>✅ Lorem ipsum exclusivas <span class="line-through text-red-500">R$00</span></li>
                            </ul>
                            <div class="mt-auto flex flex-col items-center" aria-label="Preço da oferta advanced">
                                <p class="text-xs text-gray-400 mb-2">De <span
                                        class="line-through text-red-500">R$00</span>
                                    por apenas:</p>
                                <p class="text-3xl font-bold text-green-500 mb-4">R$00</p>
                                <button
                                    class="bg-white text-black font-bold px-4 py-2 rounded hover:bg-gray-300 transition"
                                    aria-label="Selecionar oferta advanced">
                                    EU QUERO A OFERTA ADVANCED
                                </button>
                                <p class="text-xs mt-2 text-gray-400">ou 12x de <strong>R$00</strong></p>
                            </div>
                        </article>
                    </li>

                    <!-- Oferta Premium -->
                    <li class="flex flex-col">
                        <article
                            class="bg-gray-900 bg-gradient-to-br from-orange-700 via-red-800 to-yellow-600 p-1 rounded-lg shadow-xl md:col-span-2 lg:col-span-1 md:mx-auto lg:m-0 h-full flex flex-col">
                            <div class="bg-gray-900 p-6 rounded-lg text-center flex flex-col h-full">
                                <span class="bg-green-600 text-black text-xs font-bold px-3 py-1 rounded-full mb-2"
                                    aria-label="Oferta mais vendida">
                                    MAIS VENDIDO
                                </span>
                                <h4 id="oferta-premium" class="text-2xl font-bold text-orange-400 mb-2">Lorem ipsum
                                    premium
                                </h4>
                                <ul class="text-sm mb-4 space-y-1 text-left">
                                    <li>✅ Lorem ipsum pack <span class="line-through text-red-500">R$00</span></li>
                                    <li>✅ Lorem ipsum presets <span class="line-through text-red-500">R$00</span></li>
                                    <li>✅ Lorem ipsum docs <span class="line-through text-red-500">R$00</span></li>
                                    <li>✅ Lorem ipsum atualizações <span class="line-through text-red-500">R$00</span>
                                    </li>
                                    <li>✅ Lorem ipsum aulas <span class="line-through text-red-500">R$00</span></li>
                                    <li>✅ Lorem ipsum trabalhos <span class="text-red-500">🔥</span></li>
                                    <li>✅ Lorem ipsum networking <span class="text-red-500 italic">inacreditável</span>
                                    </li>
                                    <li>✅ Lorem ipsum legendas <span class="line-through text-red-500">R$00</span></li>
                                </ul>
                                <div class="mt-auto flex flex-col items-center" aria-label="Preço da oferta premium">
                                    <p class="text-xs mb-2">De <span class="line-through text-red-500">R$00</span> por
                                        apenas:
                                    </p>
                                    <p class="text-3xl font-bold text-green-300 mb-4">R$00</p>
                                    <button
                                        class="bg-red-700 hover:bg-red-900 text-white font-bold px-4 py-2 rounded transition"
                                        aria-label="Selecionar oferta premium">
                                        EU QUERO A OFERTA PREMIUM
                                    </button>
                                    <p class="text-xs mt-2 text-gray-400">ou 12x de <strong>R$00</strong></p>
                                </div>
                            </div>
                        </article>
                    </li>
                </ul>
            </div>
        </section>

        <section class="relative" aria-labelledby="faq-title">
            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">
                

                <h2 id="faq-title" class="text-2xl font-semibold">Lorem ipsum</h2>

                <div class="text-left">
                    <h3 class="text-2xl font-bold mb-6">Perguntas mais frequentes:</h3>

                    <!-- Item 1 -->
                    <div class="border-b border-gray-300 py-4">
                        <button type="button"
                            class="border-0 hover:none faq-toggle flex justify-between w-full text-left text-red-500 font-semibold text-sm"
                            aria-expanded="false" aria-controls="faq-answer-1">
                            <span>🔸 Lorem ipsum dolor sit amet?</span>
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="faq-answer-1" class="faq-answer hidden mt-2 text-sm" role="region">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="border-b border-gray-300 py-4">
                        <button type="button"
                            class="border-0 hover:none faq-toggle flex justify-between w-full text-left text-red-500 font-semibold text-sm"
                            aria-expanded="false" aria-controls="faq-answer-2">
                            <span>🔸 Lorem ipsum consectetur adipiscing?</span>
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="faq-answer-2" class="faq-answer hidden mt-2 text-sm" role="region">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="border-b border-gray-300 py-4">
                        <button type="button"
                            class="border-0 hover:none faq-toggle flex justify-between w-full text-left text-red-500 font-semibold text-sm"
                            aria-expanded="false" aria-controls="faq-answer-3">
                            <span>🔸 Lorem ipsum dolor sit?</span>
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="faq-answer-3" class="faq-answer hidden mt-2 text-sm" role="region">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="border-b border-gray-300 py-4">
                        <button type="button"
                            class="border-0 hover:none faq-toggle flex justify-between w-full text-left text-red-500 font-semibold text-sm"
                            aria-expanded="false" aria-controls="faq-answer-4">
                            <span>🔸 Lorem ipsum sit amet, consectetur?</span>
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="faq-answer-4" class="faq-answer hidden mt-2 text-sm" role="region">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore.
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="border-b border-gray-300 py-4">
                        <button type="button"
                            class="border-0 hover:none faq-toggle flex justify-between w-full text-left text-red-500 font-semibold text-sm"
                            aria-expanded="false" aria-controls="faq-answer-5">
                            <span>🔸 Lorem ipsum dolor sit amet?</span>
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="faq-answer-5" class="faq-answer hidden mt-2 text-sm" role="region">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore.
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="border-b border-gray-300 py-4">
                        <button type="button"
                            class="border-0 hover:none faq-toggle flex justify-between w-full text-left text-red-500 font-semibold text-sm"
                            aria-expanded="false" aria-controls="faq-answer-6">
                            <span>🔸 Lorem ipsum dolor sit amet, consectetur?</span>
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="faq-answer-6" class="faq-answer hidden mt-2 text-sm" role="region">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="relative" aria-labelledby="quem-sou-titulo">
            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">
                

                <h2 id="quem-sou-titulo" class="text-2xl font-semibold text-center md:text-left">
                    Lorem ipsum
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center text-left">
                    <div>
                        <p class="text-base leading-relaxed">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.
                        </p>
                    </div>

                    <div class="flex justify-center" role="figure" aria-label="Imagem de apresentação de quem sou">
                        <img src="{{ asset('storage/rc-studio/rafa-quem-sou-eu.webp') }}"
                            alt="Rafa: produtor musical e responsável pelo RC Studio"
                            title="Rafa: produtor musical no RC Studio" class="rounded shadow-md w-full max-w-xs"
                            loading="lazy" decoding="async" fetchpriority="low" />
                    </div>
                </div>
            </div>
        </section>

        <section class="relative" aria-labelledby="fale-conosco-titulo">
            <div class="max-w-4xl mx-auto p-4 text-center space-y-6">
                

                <h2 id="fale-conosco-titulo" class="text-2xl font-semibold">
                    Lorem ipsum dolor?
                </h2>

                <a href="https://wa.me/5534991256642" target="_blank" rel="noopener noreferrer"
                    class="inline-block bg-green-800 text-white py-2 px-4 rounded hover:bg-green-900 transition-colors"
                    role="button" aria-label="Entrar em contato via WhatsApp - abre em nova aba">
                    Entrar em contato via WhatsApp
                </a>

                <div class="mt-4" role="contentinfo" aria-live="polite">
                    <p>Lorem ipsum</p>
                    <p class="text-sm">© 2025 - Todos os direitos reservados</p>
                </div>
            </div>
        </section>


    </div>

    <script>
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                const icon = button.querySelector('i');
                const isOpen = !answer.classList.contains('hidden');

                // Fecha todos os outros
                document.querySelectorAll('.faq-answer').forEach(a => a.classList.add('hidden'));
                document.querySelectorAll('.faq-toggle').forEach(b => {
                    b.setAttribute('aria-expanded', 'false');
                    b.querySelector('i').classList.replace('fa-minus', 'fa-plus');
                });

                // Abre/fecha o clicado
                if (!isOpen) {
                    answer.classList.remove('hidden');
                    button.setAttribute('aria-expanded', 'true');
                    icon.classList.replace('fa-plus', 'fa-minus');
                }
            });
        });
    </script>
</body>

</html>

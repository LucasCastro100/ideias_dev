<div x-data="{
    slideIndex: @entangle('currentSlide'),
    slides: {{ json_encode($slides) }},
    isFirst() { return this.slideIndex === 0; },
    isLast() { return this.slideIndex === this.slides.length - 1; }
}" class="relative w-full h-full bg-white flex items-center justify-center overflow-hidden"
    style="background-image: url('{{ asset('storage/tbr/image/bg_pptx.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="slideIndex === index" x-transition
            class="absolute inset-0 flex flex-col items-center justify-center px-8 text-center bg-black bg-opacity-40 overflow-hidden">
            <!-- Slide de Introdução -->
            <template x-if="slide.type === 'intro'">
                <h1 class="text-8xl font-extrabold text-white max-w-5xl mx-auto" x-text="slide.title"></h1>
            </template>

            <!-- Slide de Premiação -->
            <template x-if="slide.type === 'award'">
                <div class="space-y-6">
                    <h1 class="text-7xl font-bold text-white"
                        x-text="slide.categoryLabel + ' - ' + slide.modalidadeLabel"></h1>
                    <h2 class="text-6xl text-yellow-400 font-bold" x-text="slide.posNumber + 'º Lugar'"></h2>
                    <template x-if="slide.teamName">
                        <h2 class="text-7xl font-semibold text-blue-300 mt-6" x-text="slide.teamName"></h2>
                    </template>
                </div>
            </template>

            <!-- Slide de Agradecimento -->
            <!-- Slide de Agradecimento -->
            <template x-if="slide.type === 'thankyou'">
                <h1 class="text-8xl font-extrabold text-white max-w-5xl mx-auto" x-text="slide.message"></h1>
            </template>

        </div>
    </template>

    <!-- Botão Voltar -->
    <button x-show="!isFirst()" wire:click="prev"
        class="absolute left-8 top-1/2 transform -translate-y-1/2 px-4 py-3 bg-gray-900 bg-opacity-70 text-white rounded hover:bg-gray-700 transition"
        aria-label="Anterior" x-transition.opacity>
        <i class="fas fa-chevron-left fa-2xl"></i>
    </button>

    <!-- Botão Avançar -->
    <button x-show="!isLast()" wire:click="next"
        class="absolute right-8 top-1/2 transform -translate-y-1/2 px-4 py-3 bg-gray-900 bg-opacity-70 text-white rounded hover:bg-gray-700 transition"
        aria-label="Próximo" x-transition.opacity>
        <i class="fas fa-chevron-right fa-2xl"></i>
    </button>
</div>

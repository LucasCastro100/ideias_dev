<div x-data="{
    slideIndex: @entangle('currentSlide'),
    slides: {{ json_encode($slides) }},
    isFirst() { return this.slideIndex === 0; },
    isLast() { return this.slideIndex === this.slides.length - 1; },
    next() { if (!this.isLast()) this.slideIndex++; },
    prev() { if (!this.isFirst()) this.slideIndex--; }
}" x-init="window.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
});"
    class="fixed inset-0 bg-gray-950 flex items-center justify-center overflow-hidden">
    
    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="slideIndex === index" x-transition
            class="absolute inset-0 flex flex-col items-center justify-center px-8 text-center overflow-hidden">
            
            <template x-if="slide.type === 'intro'">
                <h1 class="text-8xl font-extrabold text-gray-100 max-w-5xl mx-auto" x-text="slide.title"></h1>
            </template>

            <template x-if="slide.type === 'award'">
                <div class="space-y-6">
                    <h1 class="text-7xl font-bold text-gray-300"
                        x-text="slide.categoryLabel + ' - ' + slide.modalidadeLabel"></h1>
                    <h2 class="text-6xl text-yellow-400 font-bold" x-text="slide.posNumber + 'º Lugar'"></h2>
                    <template x-if="slide.teamName">
                        <h2 class="text-7xl font-semibold text-blue-400 mt-6" x-text="slide.teamName"></h2>
                    </template>
                </div>
            </template>

            <template x-if="slide.type === 'thankyou'">
                <h1 class="text-8xl font-extrabold text-gray-100 max-w-5xl mx-auto" x-text="slide.message"></h1>
            </template>

        </div>
    </template>

    <div class="absolute bottom-4 left-0 right-0 flex justify-center items-center gap-6">
        <button x-show="!isFirst()" @click="prev"
            class="px-4 py-3 bg-gray-800 text-white rounded hover:bg-gray-700 transition"
            aria-label="Anterior" x-transition.opacity>
            <i class="fas fa-chevron-left fa-2xl"></i>
        </button>

        <button x-show="!isLast()" @click="next"
            class="px-4 py-3 bg-gray-800 text-white rounded hover:bg-gray-700 transition"
            aria-label="Próximo" x-transition.opacity>
            <i class="fas fa-chevron-right fa-2xl"></i>
        </button>
    </div>
</div>
@props(['message' => session('toast.message'), 'style' => session('toast.style', 'success')])

<div x-data="{
    show: {{ $message ? 'true' : 'false' }},
    message: {{ json_encode($message ?? '') }},
    style: {{ json_encode($style) }},
    timer: null,
    dismiss() {
        this.show = false;
        clearTimeout(this.timer);
    },
    showToast(msg, style) {
        this.message = msg;
        this.style = style;
        this.show = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.show = false, 5000);
    }
}"
    x-init="
        if (show) timer = setTimeout(() => show = false, 5000);
        window.addEventListener('toast-message', (e) => showToast(e.detail.message, e.detail.style));
        window.addEventListener('banner-message', (e) => showToast(e.detail.message, e.detail.style));
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="fixed top-20 right-4 z-50 max-w-sm w-full pointer-events-auto"
    style="display: none;"
    role="alert">
    <div :class="{
        'bg-green-600': style == 'success',
        'bg-red-600': style == 'danger',
        'bg-yellow-600': style == 'warning',
        'bg-gray-600': style != 'success' && style != 'danger' && style != 'warning'
    }" class="rounded-lg shadow-xl border border-white/10 p-4 flex items-start gap-3">
        <span x-show="style == 'success'" class="shrink-0">
            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <span x-show="style == 'danger'" class="shrink-0">
            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </span>
        <span x-show="style == 'warning'" class="shrink-0">
            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </span>
        <span x-show="style != 'success' && style != 'danger' && style != 'warning'" class="shrink-0">
            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
        </span>

        <p class="flex-1 text-white text-sm font-medium leading-5" x-text="message"></p>

        <button type="button" @click="dismiss()" class="shrink-0 text-white/60 hover:text-white transition">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

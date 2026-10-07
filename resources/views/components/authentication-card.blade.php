<div class="min-h-screen flex flex-col justify-center items-center py-6 sm:py-10 bg-gray-100 dark:bg-gray-900">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-8 px-6 py-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>

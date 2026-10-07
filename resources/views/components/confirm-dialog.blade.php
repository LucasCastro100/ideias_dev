@props(['id' => null, 'message' => 'Tem certeza?'])

@if ($id)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex flex-col items-center justify-center gap-2">
                <div class="shrink-0 flex items-center justify-center size-10 rounded-full bg-red-900/50">
                    <svg class="size-5 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="flex-1 text-center">
                    <h3 class="text-lg font-semibold text-gray-100">Confirmar</h3>
                    <p class="text-sm text-gray-400 mt-2">{{ $message }}</p>
                </div>
            </div>
            <div class="flex justify-between gap-3 mt-6">
                <button type="button" wire:click="cancelConfirmation" class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition text-sm">
                    Cancelar
                </button>
                <button type="button" wire:click="executeAction" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
@endif

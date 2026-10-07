@props(['model', 'label' => null, 'placeholder' => ''])
@php $editorId = 'richtext_' . \Illuminate\Support\Str::random(8); @endphp

<div>
    @if ($label)
        <label class="block text-sm text-gray-300 mb-1">{{ $label }}</label>
    @endif
    <div wire:ignore class="border border-gray-700 rounded-lg overflow-hidden">
        <div class="flex flex-wrap gap-0.5 p-2 bg-gray-800 border-b border-gray-700">
            <button type="button" onclick="let ed=document.getElementById('{{ $editorId }}');ed.focus();document.execCommand('formatBlock', false, '<p>')||document.execCommand('formatBlock', false, 'p')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Parágrafo">P</button>
            <button type="button" onclick="let ed=document.getElementById('{{ $editorId }}');ed.focus();document.execCommand('formatBlock', false, '<h1>')||document.execCommand('formatBlock', false, 'h1')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 1">H1</button>
            <button type="button" onclick="let ed=document.getElementById('{{ $editorId }}');ed.focus();document.execCommand('formatBlock', false, '<h2>')||document.execCommand('formatBlock', false, 'h2')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 2">H2</button>
            <button type="button" onclick="let ed=document.getElementById('{{ $editorId }}');ed.focus();document.execCommand('formatBlock', false, '<h3>')||document.execCommand('formatBlock', false, 'h3')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Título 3">H3</button>
            <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('bold')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded font-bold" title="Negrito"><b>B</b></button>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('italic')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded italic" title="Itálico"><i>I</i></button>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('underline')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded underline" title="Sublinhado"><u>U</u></button>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('strikeThrough')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded line-through" title="Tachado"><s>S</s></button>
            <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('insertUnorderedList')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Lista">☰</button>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('insertOrderedList')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Lista numerada">#</button>
            <span class="w-px h-5 bg-gray-700 mx-1 self-center"></span>
            <button type="button" onclick="const e=document.getElementById('{{ $editorId }}');e.focus();document.execCommand('removeFormat')" class="px-2 py-1 text-xs text-gray-300 hover:bg-gray-700 rounded" title="Limpar formatação">✕</button>
        </div>
        <div id="{{ $editorId }}" contenteditable="true"
            x-data="{ init() { $el.innerHTML = $wire.{{ $model }} ?? ''; $el.addEventListener('input', () => $wire.set('{{ $model }}', $el.innerHTML)); } }"
            class="bg-gray-900 text-gray-200 p-4 min-h-[150px] focus:outline-none overflow-y-auto leading-relaxed"
            style="white-space: pre-wrap; word-wrap: break-word;"
            placeholder="{{ $placeholder }}"></div>
    </div>
    @error($model) <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
</div>

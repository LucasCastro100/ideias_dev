@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'mask' => null,
    'placeholder' => null,
    'disabled' => false,
    'required' => false,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block font-semibold text-gray-300 mb-1">
            {{ $label }}
            @if ($required)<span class="text-red-400">*</span>@endif
        </label>
    @endif

    <input
        id="{{ $name }}"
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        @if ($mask === 'phone')
            x-on:input="
                let val = $event.target.value.replace(/\D/g, '').substring(0, 11);
                val = val.replace(/^(\d{2})(\d)/, '($1) $2')
                         .replace(/(\d{5})(\d)/, '$1-$2');
                $event.target.value = val"
            maxlength="15"
        @elseif ($mask === 'document')
            x-on:input="
                let val = $event.target.value.replace(/\D/g, '').substring(0, 14);
                if (val.length <= 11) {
                    val = val.replace(/(\d{3})(\d)/, '$1.$2')
                             .replace(/(\d{3})(\d)/, '$1.$2')
                             .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    val = val.replace(/^(\d{2})(\d)/, '$1.$2')
                             .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                             .replace(/\.(\d{3})(\d)/, '.$1/$2')
                             .replace(/(\d{4})(\d)/, '$1-$2');
                }
                $event.target.value = val"
            maxlength="18"
        @endif
        {!! $attributes->merge(['class' => 'w-full bg-gray-800 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500']) !!}
    >

    @if ($name)
        @error($name)
            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
        @enderror
    @endif
</div>

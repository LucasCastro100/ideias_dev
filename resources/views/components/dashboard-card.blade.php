<div @class([
    'bg-gray-900 border border-gray-800 rounded-lg p-2.5',
    'lg:col-span-2' => ($span ?? false),
])>
    <p class="text-sm text-gray-200 uppercase tracking-wide">{{ $label }}</p>
    <p class="text-xl font-bold {{ $color ?? 'text-blue-400' }}">{{ $value }}</p>
    @if (!empty($extra))
        {!! $extra !!}
    @endif
</div>
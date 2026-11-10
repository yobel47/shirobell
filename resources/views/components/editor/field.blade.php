@props(['model', 'label', 'type' => 'text', 'hint' => null, 'rows' => 4])
@php $id = str_replace('.', '-', $model); @endphp
<div>
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" wire:model="{{ $model }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'form-input']) }}></textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" wire:model="{{ $model }}" {{ $attributes->merge(['class' => 'form-input']) }}>
    @endif
    @if ($hint)
        <p class="mt-1.5 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    @error($model) <p class="form-error">{{ $message }}</p> @enderror
</div>

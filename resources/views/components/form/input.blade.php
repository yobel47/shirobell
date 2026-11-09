@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])
<div>
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        {{ $attributes->merge(['class' => 'form-input']) }}
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
    >
    @if ($hint)
        <p class="mt-1.5 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="form-error">{{ $message }}</p>
    @enderror
</div>

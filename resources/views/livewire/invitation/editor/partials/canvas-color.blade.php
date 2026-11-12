{{-- Color field for the canvas editor: theme tokens (follow the variant) or a custom hex. $model is an Alpine expression. --}}
<div class="space-y-1.5">
    <p class="text-xs font-medium text-gray-600">{{ $label }}</p>
    <div class="flex flex-wrap items-center gap-1.5">
        <template x-for="t in tokens" :key="t">
            <button type="button" x-on:click="{{ $model }} = t; commit()" :title="t"
                    class="h-6 w-6 rounded-full ring-offset-1"
                    :class="{{ $model }} === t ? 'ring-2 ring-sky-500' : 'ring-1 ring-gray-300'"
                    :style="`background:${swatch(t)}`"></button>
        </template>
        @if ($none ?? false)
            <button type="button" x-on:click="{{ $model }} = 'none'; commit()" title="Tanpa warna"
                    class="h-6 w-6 rounded-full bg-[linear-gradient(135deg,#fff_45%,#ef4444_45%,#ef4444_55%,#fff_55%)] ring-offset-1"
                    :class="{{ $model }} === 'none' ? 'ring-2 ring-sky-500' : 'ring-1 ring-gray-300'"></button>
        @endif
        <label class="relative h-6 w-6 cursor-pointer overflow-hidden rounded-full ring-1 ring-gray-300" title="Warna lain">
            <span class="absolute inset-0 bg-[conic-gradient(red,yellow,lime,cyan,blue,magenta,red)]"></span>
            <input type="color" class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                   :value="{{ $model }}?.startsWith('#') ? {{ $model }} : '#888888'"
                   x-on:input="{{ $model }} = $event.target.value" x-on:change="commit()">
        </label>
    </div>
</div>

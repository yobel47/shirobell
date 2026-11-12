{{-- One canvas sticker, filling its box. $set: corner | crown | divider | scene; $key: family or scene. --}}
@switch($set)
    @case('corner')
        <svg viewBox="0 0 120 120" class="h-full w-full" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            @include('themes._engine.ornaments.corner', ['family' => $key])
        </svg>
        @break
    @case('crown')
        @include('themes._engine.ornaments.crown', ['engine' => ['ornament' => $key], 'class' => 'h-full w-full'])
        @break
    @case('divider')
        <div class="flex h-full w-full items-center [&_div]:p-0 [&_svg]:h-full [&_svg]:w-full">
            @include('themes._engine.partials.ornament', ['engine' => ['ornament' => $key]])
        </div>
        @break
    @default
        @include('themes._engine.illustrations.scene', ['scene' => $key, 'class' => 'h-full w-full'])
@endswitch

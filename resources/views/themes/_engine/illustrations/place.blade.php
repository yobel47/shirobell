{{-- Places the theme illustration: hanging scenes at the top, landscapes at the bottom. --}}
@php
    $scene = $engine['illustration'] ?? null;
    $hanging = in_array($scene, ['lampion', 'melati', 'taman'], true);
@endphp
@if ($scene)
    <div @class([
        'pointer-events-none absolute inset-x-0 flex justify-center',
        'top-0' => $hanging,
        'bottom-0' => ! $hanging,
        $wrapClass ?? '',
    ]) aria-hidden="true">
        @include('themes._engine.illustrations.scene', ['scene' => $scene, 'class' => $class ?? 'w-full max-w-md max-h-[30vh]'])
    </div>
@endif

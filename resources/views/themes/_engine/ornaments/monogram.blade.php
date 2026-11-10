{{-- Shown instead of an empty frame when no photo is uploaded: initials inside an ornamental frame. --}}
<div class="relative flex h-full w-full items-center justify-center overflow-hidden bg-t-soft">
    @if ($pattern)
        <div class="{{ $pattern }} absolute inset-0"></div>
    @endif
    <div class="absolute inset-0 bg-linear-to-b from-t-soft/30 via-t-soft/80 to-t-soft/30"></div>
    @if ($withCorners ?? true)
        @include('themes._engine.ornaments.corners', ['size' => 'h-12 w-12', 'gap' => '1'])
    @endif
    <div class="relative px-2 text-center">
        <p class="font-script text-5xl leading-none text-t-primary">{{ $initials }}</p>
        <div class="mx-auto mt-3 h-px w-12 bg-t-accent"></div>
    </div>
</div>

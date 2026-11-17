{{--
    Section heading for engine themes: crown ornament, eyebrow, then the title
    in the theme's heading style ($h2). Params: $title, $eyebrow (optional),
    $light (on the primary-colored countdown band).
--}}
<header class="text-center" {!! $reveal !!}>
    @include('themes._engine.ornaments.crown', ['class' => 'h-9 w-36'])
    @isset($eyebrow)
        <p @class(['tc-eyebrow mt-1', 'text-t-on/80!' => $light ?? false])>{{ $eyebrow }}</p>
    @endisset
    <h2 class="{{ $h2 }} tc-title mt-3 {{ ($light ?? false) ? 'text-t-on!' : '' }}">{{ $title }}</h2>
</header>

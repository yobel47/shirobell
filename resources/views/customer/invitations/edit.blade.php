@php
    $previewUrl = route('app.invitations.preview', [$invitation, 'buka' => 1, 'frame' => 1]);
@endphp
<x-layouts.app :title="'Edit '.$invitation->coupleName()" wide>
    <x-invitation-header :invitation="$invitation" active="editor" />

    {{--
        Live preview: the frame reloads after every save (the editor tabs dispatch
        "notify"). Desktop shows it beside the form; smaller screens open it full screen.
    --}}
    <div class="mt-6 lg:grid lg:grid-cols-[12rem_1fr] lg:gap-8 xl:grid-cols-[12rem_1fr_22rem]"
         x-data="{
            device: 'phone',
            open: false,
            version: 0,
            url: @js($previewUrl),
            timer: null,
            reload() { clearTimeout(this.timer); this.timer = setTimeout(() => this.version++, 300); },
            src() { return this.url + '&v=' + this.version; },
         }"
         x-on:notify.window="reload()">
        <nav aria-label="Bagian editor" class="-mx-4 mb-6 overflow-x-auto px-4 lg:mx-0 lg:mb-0 lg:px-0">
            <ul class="flex gap-1 lg:sticky lg:top-24 lg:flex-col">
                @foreach ($tabs as $key => [$label])
                    <li>
                        <a href="{{ route('app.invitations.edit', [$invitation, $key]) }}"
                           @if ($tab === $key) aria-current="page" @endif
                           @class([
                               'block rounded-full px-4 py-2 text-sm font-medium whitespace-nowrap lg:rounded-xl',
                               'bg-rose-brand-500 text-white' => $tab === $key,
                               'text-ink hover:bg-rose-brand-50' => $tab !== $key,
                           ])>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="min-w-0">
            @livewire($tabs[$tab][1], ['invitation' => $invitation], key($tab))
        </div>

        {{-- Desktop preview column --}}
        <aside class="hidden xl:block" aria-label="Preview undangan">
            <div class="sticky top-24">
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-semibold">Preview</p>
                    <div class="flex rounded-full bg-gray-100 p-0.5 text-xs font-medium">
                        <button type="button" x-on:click="device = 'phone'" x-bind:class="device === 'phone' ? 'bg-white shadow-sm' : 'text-gray-500'" class="rounded-full px-3 py-1">HP</button>
                        <button type="button" x-on:click="device = 'desktop'" x-bind:class="device === 'desktop' ? 'bg-white shadow-sm' : 'text-gray-500'" class="rounded-full px-3 py-1">Desktop</button>
                    </div>
                </div>
                <div x-ref="box" class="relative h-[calc(100vh-10rem)] overflow-hidden rounded-[2rem] border-8 border-ink bg-white shadow-xl"
                     x-bind:class="device === 'desktop' && 'rounded-xl border-4'"
                     x-data="{ w: 0, h: 0 }" x-init="const r = () => { w = $el.clientWidth; h = $el.clientHeight }; r(); new ResizeObserver(r).observe($el)">
                    <iframe x-bind:src="src()" title="Preview undangan" loading="lazy"
                            class="absolute top-0 left-0 origin-top-left border-0"
                            x-bind:style="(() => { const dw = device === 'phone' ? 390 : 1280; const s = w / dw; return `width:${dw}px;height:${h / s}px;transform:scale(${s})`; })()"></iframe>
                </div>
                <div class="mt-3 flex justify-between text-xs">
                    <button type="button" x-on:click="version++" class="font-medium text-rose-brand-600 hover:underline">Muat ulang</button>
                    <a href="{{ route('app.invitations.preview', $invitation) }}" target="_blank" class="font-medium text-rose-brand-600 hover:underline">Buka di tab baru</a>
                </div>
            </div>
        </aside>

        {{-- Mobile / tablet: floating button + full-screen preview --}}
        <button type="button" x-on:click="open = true" class="btn btn-primary fixed right-4 bottom-4 z-30 shadow-lg xl:hidden">Preview</button>
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex flex-col bg-white xl:hidden" x-on:keydown.escape.window="open = false">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <p class="text-sm font-semibold">Preview</p>
                <button type="button" x-on:click="open = false" class="btn btn-ghost px-3 py-1.5">Tutup</button>
            </div>
            <template x-if="open">
                <iframe x-bind:src="src()" title="Preview undangan" class="w-full flex-1 border-0"></iframe>
            </template>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app :title="'Edit '.$invitation->coupleName()">
    <x-invitation-header :invitation="$invitation" active="editor" />

    <div class="mt-6 lg:grid lg:grid-cols-[13rem_1fr] lg:gap-8">
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
    </div>
</x-layouts.app>

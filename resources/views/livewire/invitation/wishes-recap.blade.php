<div class="space-y-6">
    <dl class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="card p-4">
            <dt class="text-xs text-gray-500">Hadir</dt>
            <dd class="mt-1 font-serif text-2xl font-semibold text-emerald-700">{{ $stats['yes'] }} <span class="text-sm font-normal text-gray-500">RSVP / {{ $stats['yes_pax'] }} orang</span></dd>
        </div>
        <div class="card p-4">
            <dt class="text-xs text-gray-500">Tidak hadir</dt>
            <dd class="mt-1 font-serif text-2xl font-semibold">{{ $stats['no'] }}</dd>
        </div>
        <div class="card p-4">
            <dt class="text-xs text-gray-500">Masih ragu</dt>
            <dd class="mt-1 font-serif text-2xl font-semibold text-amber-700">{{ $stats['maybe'] }}</dd>
        </div>
        <div class="card p-4">
            <dt class="text-xs text-gray-500">Ucapan</dt>
            <dd class="mt-1 font-serif text-2xl font-semibold">{{ $stats['messages'] }}</dd>
        </div>
    </dl>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="-mx-4 flex gap-1 overflow-x-auto px-4 sm:mx-0 sm:px-0">
            @foreach ($filters as $value => $label)
                <button type="button" wire:click="$set('filter', '{{ $value }}')" @class([
                    'rounded-full px-4 py-1.5 text-sm font-medium whitespace-nowrap',
                    'bg-rose-brand-500 text-white' => $filter === $value,
                    'bg-white text-ink ring-1 ring-gray-200 hover:bg-rose-brand-50' => $filter !== $value,
                ])>{{ $label }}</button>
            @endforeach
        </div>
        <a href="{{ route('app.invitations.wishes.export', $invitation) }}" class="btn btn-outline shrink-0">Unduh CSV</a>
    </div>

    <div class="space-y-3">
        @forelse ($wishes as $wish)
            <article wire:key="wish-{{ $wish->id }}" @class(['card p-4', 'opacity-60' => $wish->is_hidden])>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold">{{ $wish->name }}</p>
                            @if ($wish->attendance)
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $wish->attendance->badgeClass() }}">{{ $wish->attendance->label() }}@if ($wish->pax) ({{ $wish->pax }} orang)@endif</span>
                            @endif
                            @if ($wish->guest)
                                <span class="rounded-full bg-rose-brand-50 px-2 py-0.5 text-xs text-rose-brand-700">Tamu terdaftar</span>
                            @endif
                            @if ($wish->is_hidden)
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">Disembunyikan</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-gray-400">{{ $wish->created_at->translatedFormat('d M Y, H:i') }}</p>
                        @if ($wish->message)
                            <p class="mt-2 text-sm whitespace-pre-line text-gray-700">{{ $wish->message }}</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 gap-1">
                        @if ($wish->message)
                            <button type="button" wire:click="toggleHidden({{ $wish->id }})" class="btn btn-ghost px-3 py-1.5">{{ $wish->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}</button>
                        @endif
                        <button type="button" wire:click="delete({{ $wish->id }})" wire:confirm="Hapus data ini?" class="btn btn-ghost px-3 py-1.5 text-red-600">Hapus</button>
                    </div>
                </div>
            </article>
        @empty
            <div class="card py-10 text-center text-sm text-gray-500">Belum ada RSVP atau ucapan.</div>
        @endforelse

        {{ $wishes->links() }}
    </div>
</div>

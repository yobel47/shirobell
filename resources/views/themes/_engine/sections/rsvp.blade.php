{{-- Section "rsvp" of the engine theme (ADR 012: order and visibility are configurable). --}}
@if ($ticketQr ?? null)
    <x-invitation.ticket :qr="$ticketQr" :guest="$guest" />
@endif

{{-- RSVP & wishes --}}
@if ($showInteraction)
    <section id="rsvp" class="px-6 py-20 text-center">
        @include('themes._engine.partials.heading', [
            'eyebrow' => 'Kehadiran',
            'title' => $invitation->sectionEnabled('rsvp') && $invitation->sectionEnabled('wishes') ? 'Konfirmasi & Ucapan' : ($invitation->sectionEnabled('rsvp') ? 'Konfirmasi Kehadiran' : 'Ucapan & Doa'),
        ])
        <p class="tc-pretty mx-auto mt-4 mb-10 max-w-xs text-sm leading-relaxed opacity-80">Kehadiran dan doa restu Anda sangat berarti bagi kami.</p>
        @if ($demo ?? false)
            @include('themes._engine.livewire.wishes', [
                'submitted' => false, 'rsvpEnabled' => true, 'wishesEnabled' => true, 'attendance' => 'yes',
                'attendanceOptions' => \App\Enums\Attendance::cases(), 'maxPax' => 2, 'preview' => true,
                'wishes' => $demoWishes, 'total' => $demoWishes->count(),
            ])
        @else
            <livewire:invitation.public-wishes :invitation="$invitation" :guest-id="$guest?->id" :guest-name="$guestName" :preview="$preview" />
        @endif
    </section>
@endif

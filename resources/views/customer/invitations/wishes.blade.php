<x-layouts.app :title="'RSVP & Ucapan '.$invitation->coupleName()">
    <x-invitation-header :invitation="$invitation" active="rsvp" />

    <div class="mt-6">
        <livewire:invitation.wishes-recap :invitation="$invitation" />
    </div>
</x-layouts.app>

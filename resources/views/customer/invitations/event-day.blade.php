<x-layouts.app :title="'Hari-H '.$invitation->coupleName()">
    <x-invitation-header :invitation="$invitation" active="hari-h" />
    <div class="mt-6">
        <livewire:invitation.event-day-manager :invitation="$invitation" />
    </div>
</x-layouts.app>

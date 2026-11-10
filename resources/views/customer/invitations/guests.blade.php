<x-layouts.app :title="'Tamu '.$invitation->coupleName()">
    <x-invitation-header :invitation="$invitation" active="tamu" />

    <div class="mt-6">
        <livewire:invitation.guest-manager :invitation="$invitation" />
    </div>
</x-layouts.app>

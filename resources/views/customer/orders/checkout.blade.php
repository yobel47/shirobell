<x-layouts.app title="Pembayaran">
    <div class="mb-6">
        <a href="{{ route('app.invitations.edit', $invitation) }}" class="back-link">&larr; Kembali ke editor</a>
        <h1 class="mt-4 page-title">Pembayaran</h1>
    </div>

    <livewire:payment.checkout :invitation="$invitation" :product="$product" />
</x-layouts.app>

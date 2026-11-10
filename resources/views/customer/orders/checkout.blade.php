<x-layouts.app title="Pembayaran">
    <div class="mb-6">
        <a href="{{ route('app.invitations.edit', $invitation) }}" class="text-sm text-gray-500 hover:text-rose-brand-600">&larr; Kembali ke editor</a>
        <h1 class="mt-1 font-serif text-3xl font-semibold">Pembayaran</h1>
    </div>

    <livewire:payment.checkout :invitation="$invitation" :product="$product" />
</x-layouts.app>

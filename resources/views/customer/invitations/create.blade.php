<x-layouts.app title="Buat Undangan">
    <div class="mx-auto mb-8 max-w-2xl">
        <a href="{{ route('app.dashboard') }}" class="back-link">&larr; Undangan Saya</a>
        <h1 class="mt-4 page-title">Buat undangan</h1>
    </div>

    <livewire:invitation.create-wizard />
</x-layouts.app>

<x-layouts.app title="Buat Undangan">
    <div class="mx-auto mb-8 max-w-2xl">
        <a href="{{ route('app.dashboard') }}" class="text-sm text-gray-500 hover:text-rose-brand-600">&larr; Undangan Saya</a>
        <h1 class="mt-2 font-serif text-3xl font-semibold">Buat undangan</h1>
    </div>

    <livewire:invitation.create-wizard />
</x-layouts.app>

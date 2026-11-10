{{-- Fallback markup for themes without their own livewire/wishes view. --}}
<div class="space-y-6">
    <form wire:submit="submit" class="space-y-3">
        <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
        <input type="text" wire:model="name" placeholder="Nama" maxlength="100" class="w-full rounded border px-3 py-2">
        @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @if ($rsvpEnabled)
            <select wire:model.live="attendance" class="w-full rounded border px-3 py-2">
                <option value="">Konfirmasi kehadiran</option>
                @foreach ($attendanceOptions as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
        @endif
        @if ($wishesEnabled)
            <textarea wire:model="message" rows="3" maxlength="500" placeholder="Ucapan & doa" class="w-full rounded border px-3 py-2"></textarea>
        @endif
        @error('form') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        <button type="submit">Kirim</button>
    </form>
    @foreach ($wishes as $wish)
        <p><strong>{{ $wish->name }}</strong>: {{ $wish->message }}</p>
    @endforeach
</div>

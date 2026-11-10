@props(['id', 'first' => false, 'last' => false])
<button type="button" wire:click="move({{ $id }}, 'up')" @disabled($first) class="btn btn-ghost px-2.5 py-1.5" aria-label="Pindah ke atas">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m18 15-6-6-6 6"/></svg>
</button>
<button type="button" wire:click="move({{ $id }}, 'down')" @disabled($last) class="btn btn-ghost px-2.5 py-1.5" aria-label="Pindah ke bawah">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
</button>

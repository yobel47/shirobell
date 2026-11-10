<div class="space-y-8">
    <div class="rounded-3xl border border-t-accent/20 bg-t-surface px-6 py-8 shadow-sm">
        @if ($submitted)
            <div class="text-center" role="status">
                <p class="font-script text-4xl text-t-primary">Terima kasih</p>
                <p class="mt-2 text-sm text-t-ink/80">{{ $rsvpEnabled ? 'Konfirmasi' : 'Ucapan' }} Anda sudah kami terima.</p>
                <button type="button" wire:click="$set('submitted', false)" class="mt-4 text-xs font-semibold text-t-accent underline">Kirim lagi</button>
            </div>
        @else
            <form wire:submit="submit" class="space-y-4 text-left">
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label>Website <input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div>
                    <label for="wish-name" class="mb-1.5 block text-xs font-semibold tracking-wide text-t-primary uppercase">Nama</label>
                    <input id="wish-name" type="text" wire:model="name" maxlength="100" class="w-full rounded-xl border border-t-accent/30 bg-t-bg px-4 py-2.5 text-sm focus:border-t-accent focus:ring-2 focus:ring-t-accent/20 focus:outline-none">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                @if ($rsvpEnabled)
                    <fieldset>
                        <legend class="mb-1.5 block text-xs font-semibold tracking-wide text-t-primary uppercase">Konfirmasi kehadiran</legend>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($attendanceOptions as $option)
                                <label @class([
                                    'cursor-pointer rounded-xl border px-2 py-2.5 text-center text-xs font-medium transition',
                                    'border-t-primary bg-t-primary text-t-on' => $attendance === $option->value,
                                    'border-t-accent/30 bg-t-bg text-t-ink hover:border-t-accent' => $attendance !== $option->value,
                                ])>
                                    <input type="radio" wire:model.live="attendance" value="{{ $option->value }}" class="sr-only">
                                    {{ $option->label() }}
                                </label>
                            @endforeach
                        </div>
                        @error('attendance') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </fieldset>

                    @if ($attendance === 'yes')
                        <div>
                            <label for="wish-pax" class="mb-1.5 block text-xs font-semibold tracking-wide text-t-primary uppercase">Jumlah tamu</label>
                            <select id="wish-pax" wire:model="pax" class="w-full rounded-xl border border-t-accent/30 bg-t-bg px-4 py-2.5 text-sm focus:border-t-accent focus:outline-none">
                                @for ($i = 1; $i <= $maxPax; $i++)
                                    <option value="{{ $i }}">{{ $i }} orang</option>
                                @endfor
                            </select>
                            @error('pax') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif
                @endif

                @if ($wishesEnabled)
                    <div>
                        <label for="wish-message" class="mb-1.5 block text-xs font-semibold tracking-wide text-t-primary uppercase">Ucapan &amp; doa</label>
                        <textarea id="wish-message" wire:model="message" rows="4" maxlength="500" class="w-full rounded-xl border border-t-accent/30 bg-t-bg px-4 py-2.5 text-sm focus:border-t-accent focus:ring-2 focus:ring-t-accent/20 focus:outline-none"></textarea>
                        @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                @error('form') <p class="rounded-xl bg-red-50 px-4 py-2 text-xs text-red-700">{{ $message }}</p> @enderror

                <button type="submit" class="w-full rounded-full bg-t-primary px-6 py-3 text-sm font-semibold text-t-on hover:opacity-90 disabled:opacity-60" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Kirim</span>
                    <span wire:loading wire:target="submit">Mengirim...</span>
                </button>
                @if ($preview)
                    <p class="text-center text-xs text-t-ink/60">Mode preview: form aktif setelah undangan diterbitkan.</p>
                @endif
            </form>
        @endif
    </div>

    @if ($wishesEnabled)
        <div>
            <p class="text-center text-xs tracking-[0.25em] text-t-accent uppercase">{{ $total }} ucapan</p>
            <ul class="mt-4 max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                @forelse ($wishes as $wish)
                    <li wire:key="w-{{ $wish->id }}" class="rounded-2xl bg-t-surface px-5 py-4 text-left shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-t-primary">{{ $wish->name }}</p>
                            @if ($wish->attendance)
                                <span class="shrink-0 rounded-full bg-t-soft px-2 py-0.5 text-[10px] font-medium text-t-primary">{{ $wish->attendance->label() }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm leading-relaxed whitespace-pre-line text-t-ink/90">{{ $wish->message }}</p>
                        <p class="mt-2 text-[11px] text-t-ink/50">{{ $wish->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-center text-sm text-t-ink/60">Jadilah yang pertama mengirim ucapan.</li>
                @endforelse
            </ul>
            @if ($total > $wishes->count())
                <div class="mt-4 text-center">
                    <button type="button" wire:click="loadMore" class="rounded-full border border-t-primary/40 px-5 py-2 text-xs font-semibold text-t-primary hover:bg-t-soft">Lihat ucapan lainnya</button>
                </div>
            @endif
        </div>
    @endif
</div>

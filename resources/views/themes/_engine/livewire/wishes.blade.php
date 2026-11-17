<div class="space-y-10">
    <div class="rounded-3xl border border-t-accent/20 bg-t-surface px-6 py-8 shadow-sm">
        @if ($submitted)
            <div class="text-center" role="status">
                <span class="tc-rise mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-t-soft text-t-primary" aria-hidden="true"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg></span>
                <p class="mt-4 font-script text-4xl text-t-primary">Terima kasih</p>
                <p class="mt-2 text-sm text-t-ink/80">{{ $rsvpEnabled ? 'Konfirmasi' : 'Ucapan' }} Anda sudah kami terima.</p>
                <button type="button" wire:click="$set('submitted', false)" class="mt-5 text-xs font-semibold text-t-accent underline underline-offset-4">Kirim lagi</button>
            </div>
        @else
            <form wire:submit="submit" class="space-y-5 text-left">
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label>Website <input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div>
                    <label for="wish-name" class="tc-label">Nama</label>
                    <input id="wish-name" type="text" wire:model="name" maxlength="100" autocomplete="name" class="tc-input">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                @if ($rsvpEnabled)
                    <fieldset>
                        <legend class="tc-label">Konfirmasi kehadiran</legend>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($attendanceOptions as $option)
                                <label @class(['tc-choice has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-t-accent', 'is-active' => $attendance === $option->value])>
                                    <input type="radio" wire:model.live="attendance" value="{{ $option->value }}" class="sr-only">
                                    {{ $option->label() }}
                                </label>
                            @endforeach
                        </div>
                        @error('attendance') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </fieldset>

                    @if ($attendance === 'yes')
                        <div>
                            <label for="wish-pax" class="tc-label">Jumlah tamu</label>
                            <select id="wish-pax" wire:model="pax" class="tc-input">
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
                        <label for="wish-message" class="tc-label">Ucapan &amp; doa</label>
                        <textarea id="wish-message" wire:model="message" rows="4" maxlength="500" class="tc-input resize-none"></textarea>
                        @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                @error('form') <p class="rounded-xl bg-red-50 px-4 py-2 text-xs text-red-700">{{ $message }}</p> @enderror

                <button type="submit" class="tc-btn w-full py-3.5 text-sm" wire:loading.attr="disabled">
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
            <p class="tc-eyebrow tc-num">{{ $total }} ucapan</p>
            <ul class="mt-5 max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                @forelse ($wishes as $wish)
                    <li wire:key="w-{{ $wish->id }}" class="flex gap-3 rounded-2xl bg-t-surface px-4 py-4 text-left shadow-sm">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-t-soft font-heading text-sm font-semibold text-t-primary" aria-hidden="true">{{ mb_strtoupper(mb_substr($wish->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-sm font-semibold text-t-primary">{{ $wish->name }}</p>
                            @if ($wish->attendance)
                                <span class="shrink-0 rounded-full bg-t-soft px-2 py-0.5 text-[10px] font-medium text-t-primary">{{ $wish->attendance->label() }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm leading-relaxed whitespace-pre-line text-t-ink/90">{{ $wish->message }}</p>
                        <p class="mt-2 text-[11px] text-t-ink/50">{{ $wish->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-sm text-t-ink/60">Jadilah yang pertama mengirim ucapan.</li>
                @endforelse
            </ul>
            @if ($total > $wishes->count())
                <div class="mt-4 text-center">
                    <button type="button" wire:click="loadMore" class="tc-btn-outline">Lihat ucapan lainnya</button>
                </div>
            @endif
        </div>
    @endif
</div>

<div class="space-y-8">
    <div class="rounded-[2rem] border border-rg-rose/20 bg-white px-6 py-8 shadow-sm">
        @if ($submitted)
            <div class="text-center" role="status">
                <p class="font-script text-4xl text-rg-deep">Terima kasih</p>
                <p class="mt-2 text-sm text-rg-ink/80">{{ $rsvpEnabled ? 'Konfirmasi' : 'Ucapan' }} Anda sudah kami terima.</p>
                <button type="button" wire:click="$set('submitted', false)" class="mt-4 text-xs font-semibold text-rg-rose underline">Kirim lagi</button>
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
                                <label @class(['tc-choice', 'is-active' => $attendance === $option->value])>
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
                    <p class="text-center text-xs text-rg-ink/60">Mode preview: form aktif setelah undangan diterbitkan.</p>
                @endif
            </form>
        @endif
    </div>

    @if ($wishesEnabled)
        <div>
            <p class="tc-eyebrow tc-num">{{ $total }} ucapan</p>
            <ul class="mt-4 max-h-[28rem] space-y-3 overflow-y-auto pr-1">
                @forelse ($wishes as $wish)
                    <li wire:key="w-{{ $wish->id }}" class="rounded-2xl bg-white px-5 py-4 text-left shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-rg-deep">{{ $wish->name }}</p>
                            @if ($wish->attendance)
                                <span class="shrink-0 rounded-full bg-rg-blush px-2 py-0.5 text-[10px] font-medium text-rg-deep">{{ $wish->attendance->label() }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm leading-relaxed whitespace-pre-line text-rg-ink/90">{{ $wish->message }}</p>
                        <p class="mt-2 text-[11px] text-rg-ink/50">{{ $wish->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-center text-sm text-rg-ink/60">Jadilah yang pertama mengirim ucapan.</li>
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

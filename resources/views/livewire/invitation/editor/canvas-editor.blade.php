<div>
    @vite(['resources/css/themes/engine.css', 'resources/js/canvas-editor.js'])

    @if (! $isEngine)
        <div class="card">
            <h2 class="font-serif text-lg font-semibold">Desain sampul</h2>
            <p class="mt-1 text-sm text-gray-500">Desain bebas tersedia untuk tema selain Rose Garden, Classic Gold, dan Sage Minimalist. Ganti tema di tab Tema untuk memakainya.</p>
        </div>
    @else
        <div x-data="canvasEditor(@js($cover), @js($library), @js($images), @js($context))" x-on:keydown.window="key($event)" class="space-y-4" style="{{ $themeStyle }}">
            {{-- Toolbar --}}
            <div class="flex flex-wrap items-center gap-2 rounded-2xl bg-white p-2 shadow-sm ring-1 ring-gray-100">
                <button type="button" x-on:click="undo()" :disabled="history.length < 2" class="btn btn-ghost px-3 py-1.5" title="Undo (Ctrl+Z)">Undo</button>
                <button type="button" x-on:click="redo()" :disabled="!future.length" class="btn btn-ghost px-3 py-1.5" title="Redo (Ctrl+Y)">Redo</button>
                <span class="h-6 w-px bg-gray-200"></span>
                <button type="button" x-on:click="duplicate()" :disabled="!selected" class="btn btn-ghost px-3 py-1.5" title="Duplikat (Ctrl+D)">Duplikat</button>
                <button type="button" x-on:click="move(1)" :disabled="!selected" class="btn btn-ghost px-3 py-1.5">Ke depan</button>
                <button type="button" x-on:click="move(-1)" :disabled="!selected" class="btn btn-ghost px-3 py-1.5">Ke belakang</button>
                <button type="button" x-on:click="remove()" :disabled="!selected" class="btn btn-ghost px-3 py-1.5 text-red-600" title="Hapus (Delete)">Hapus</button>
                <span class="h-6 w-px bg-gray-200"></span>
                <button type="button" x-on:click="useTemplate()" class="btn btn-ghost px-3 py-1.5">Desain bawaan tema</button>
                <label class="ml-auto flex items-center gap-2 text-sm">
                    <input type="checkbox" x-model="state.enabled" x-on:change="commit()" class="rounded border-gray-300 text-rose-brand-500">
                    Pakai desain ini di undangan
                </label>
                <a href="{{ route('app.invitations.preview', $invitation) }}" target="_blank" class="btn btn-outline px-4 py-1.5">Lihat hasil</a>
                <button type="button" x-on:click="save()" :disabled="saving" class="btn btn-primary px-4 py-1.5">
                    <span x-text="saving ? 'Menyimpan...' : (dirty ? 'Simpan' : 'Tersimpan')"></span>
                </button>
            </div>

            <div class="grid gap-4 lg:grid-cols-[13rem_minmax(0,1fr)_16rem]">
                {{-- Add elements --}}
                <aside class="space-y-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div>
                        <p class="mb-2 text-xs font-semibold tracking-wide text-gray-500 uppercase">Teks</p>
                        <div class="grid grid-cols-2 gap-1.5 text-sm">
                            <button type="button" x-on:click="addText('title')" class="rounded-lg bg-gray-50 px-2 py-2 font-script text-lg hover:bg-rose-brand-50">Nama</button>
                            <button type="button" x-on:click="addText('heading')" class="rounded-lg bg-gray-50 px-2 py-2 font-serif font-semibold hover:bg-rose-brand-50">Judul</button>
                            <button type="button" x-on:click="addText('body')" class="rounded-lg bg-gray-50 px-2 py-2 hover:bg-rose-brand-50">Teks</button>
                            <button type="button" x-on:click="addText('date')" class="rounded-lg bg-gray-50 px-2 py-2 hover:bg-rose-brand-50">Tanggal</button>
                            <button type="button" x-on:click="addText('guest')" class="col-span-2 rounded-lg bg-gray-50 px-2 py-2 hover:bg-rose-brand-50">Nama tamu</button>
                        </div>
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-semibold tracking-wide text-gray-500 uppercase">Foto</p>
                        <label class="flex cursor-pointer items-center justify-center rounded-lg border border-dashed border-gray-300 px-2 py-3 text-sm text-gray-600 hover:bg-gray-50">
                            <span x-text="saving ? 'Mengunggah...' : 'Unggah foto'"></span>
                            <input type="file" accept="image/*" class="hidden" x-on:change="addPhoto($event)">
                        </label>
                        @error('photo') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-semibold tracking-wide text-gray-500 uppercase">Bentuk</p>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" x-on:click="addShape('rect')" title="Kotak" class="flex h-10 items-center justify-center rounded-lg bg-gray-50 hover:bg-rose-brand-50"><span class="h-5 w-6 rounded-sm bg-gray-400"></span></button>
                            <button type="button" x-on:click="addShape('circle')" title="Lingkaran" class="flex h-10 items-center justify-center rounded-lg bg-gray-50 hover:bg-rose-brand-50"><span class="h-6 w-6 rounded-full bg-gray-400"></span></button>
                            <button type="button" x-on:click="addShape('arch')" title="Lengkung" class="flex h-10 items-center justify-center rounded-lg bg-gray-50 hover:bg-rose-brand-50"><span class="h-6 w-5 rounded-t-full bg-gray-400"></span></button>
                            <button type="button" x-on:click="addShape('line')" title="Garis" class="flex h-10 items-center justify-center rounded-lg bg-gray-50 hover:bg-rose-brand-50"><span class="h-0.5 w-6 bg-gray-400"></span></button>
                        </div>
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-semibold tracking-wide text-gray-500 uppercase">Stiker &amp; ilustrasi</p>
                        <div class="mb-2 grid grid-cols-2 gap-1 text-xs">
                            @foreach (['corner' => 'Sudut', 'crown' => 'Mahkota', 'divider' => 'Pembatas', 'scene' => 'Ilustrasi'] as $set => $label)
                                <button type="button" x-on:click="stickerSet = '{{ $set }}'" class="rounded-full px-2 py-1" :class="stickerSet === '{{ $set }}' ? 'bg-rose-brand-500 text-white' : 'bg-gray-100'">{{ $label }}</button>
                            @endforeach
                        </div>
                        <div class="grid max-h-72 grid-cols-2 gap-1.5 overflow-y-auto pr-1">
                            @foreach ($library as $id => $svg)
                                @php [$set, $key] = explode(':', $id); @endphp
                                <button type="button" x-show="stickerSet === '{{ $set }}'" x-on:click="addSticker('{{ $set }}', '{{ $key }}')" title="{{ $key }}"
                                        class="flex h-16 items-center justify-center overflow-hidden rounded-lg bg-[var(--color-t-bg)] p-1 ring-1 ring-gray-100 hover:ring-rose-brand-300">
                                    <div class="pointer-events-none h-full w-full">{!! $svg !!}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </aside>

                {{-- Artboard --}}
                <div class="flex justify-center rounded-2xl bg-gray-100 p-4" x-on:pointerdown.self="selectedId = null">
                    <div x-ref="board" class="relative aspect-[390/844] w-full max-w-[360px] touch-none overflow-hidden shadow-xl select-none [container-type:inline-size]"
                         :style="{ backgroundColor: swatch(state.bg) }" x-on:pointerdown.self="selectedId = null">
                        @if ($pattern)
                            <div class="{{ $pattern }} pointer-events-none absolute inset-0" x-show="state.pattern"></div>
                        @endif
                        <div class="t-grain pointer-events-none absolute inset-0"></div>

                        <template x-for="el in state.elements" :key="el.id">
                            <div class="absolute cursor-move" :style="box(el)" x-show="!el.hidden"
                                 x-on:pointerdown="begin($event, el, 'move')"
                                 :class="selectedId === el.id ? 'outline-2 outline-sky-500 outline' : 'hover:outline hover:outline-1 hover:outline-sky-300'">
                                <template x-if="el.type === 'text'">
                                    <p class="h-full w-full leading-[1.15] whitespace-pre-line" :class="fontClass(el.props)" :style="textStyle(el.props)" x-text="resolve(el.props.text)"></p>
                                </template>
                                <template x-if="el.type === 'image'">
                                    <img :src="images[el.props.path]" alt="" draggable="false" class="pointer-events-none h-full w-full" :style="imageStyle(el.props)">
                                </template>
                                <template x-if="el.type === 'sticker'">
                                    <div class="pointer-events-none h-full w-full" :style="stickerStyle(el.props)" x-html="library[el.props.set + ':' + el.props.key]"></div>
                                </template>
                                <template x-if="el.type === 'shape'">
                                    <div class="h-full w-full" :style="shapeStyle(el.props)"></div>
                                </template>
                                <template x-if="el.type === 'button'">
                                    <div class="flex h-full w-full items-center justify-center font-semibold" :style="buttonStyle(el.props)" x-text="el.props.text"></div>
                                </template>

                                <template x-if="selectedId === el.id && !el.locked">
                                    <div>
                                        @foreach (['nw' => '-top-1.5 -left-1.5 cursor-nwse-resize', 'ne' => '-top-1.5 -right-1.5 cursor-nesw-resize', 'sw' => '-bottom-1.5 -left-1.5 cursor-nesw-resize', 'se' => '-bottom-1.5 -right-1.5 cursor-nwse-resize', 'n' => '-top-1.5 left-1/2 -ml-1.5 cursor-ns-resize', 's' => '-bottom-1.5 left-1/2 -ml-1.5 cursor-ns-resize', 'e' => 'top-1/2 -right-1.5 -mt-1.5 cursor-ew-resize', 'w' => 'top-1/2 -left-1.5 -mt-1.5 cursor-ew-resize'] as $handle => $pos)
                                            <span class="absolute z-10 h-3 w-3 rounded-full border-2 border-sky-500 bg-white {{ $pos }}" x-on:pointerdown="begin($event, el, 'resize', '{{ $handle }}')"></span>
                                        @endforeach
                                        <span class="absolute -top-8 left-1/2 z-10 -ml-2.5 h-5 w-5 cursor-grab rounded-full border-2 border-sky-500 bg-white" title="Putar (Shift: 15 derajat)" x-on:pointerdown="begin($event, el, 'rotate')"></span>
                                        <span class="absolute -top-3 left-1/2 h-3 w-px bg-sky-500"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-for="(g, i) in guides" :key="i">
                            <div class="pointer-events-none absolute z-20 bg-pink-500" :style="guideStyle(g)"></div>
                        </template>
                    </div>
                </div>

                {{-- Properties & layers --}}
                <aside class="space-y-4 rounded-2xl bg-white p-4 text-sm shadow-sm ring-1 ring-gray-100">
                    <template x-if="!selected">
                        <div class="space-y-4">
                            <p class="font-semibold">Latar</p>
                            @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Warna latar', 'model' => 'state.bg'])
                            @if ($pattern)
                                <label class="flex items-center gap-2"><input type="checkbox" x-model="state.pattern" x-on:change="commit()" class="rounded border-gray-300"> Tampilkan motif tema</label>
                            @endif
                            <p class="rounded-xl bg-gray-50 p-3 text-xs leading-relaxed text-gray-500">
                                Klik elemen untuk mengubahnya. Geser untuk memindahkan, tarik titik sudut untuk mengubah ukuran, tarik titik atas untuk memutar.
                                Pintasan: Ctrl+Z, Ctrl+Y, Ctrl+D, Delete, panah (Shift = 10).
                                Teks <code>{groom}</code> <code>{bride}</code> <code>{date}</code> <code>{guest}</code> otomatis diganti nama dan tanggal.
                            </p>
                        </div>
                    </template>

                    <template x-if="selected">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold" x-text="label(selected)"></p>
                                <div class="flex gap-1">
                                    <button type="button" x-on:click="selected.locked = !selected.locked; commit()" class="rounded px-2 py-0.5 text-xs" :class="selected.locked ? 'bg-amber-100 text-amber-800' : 'bg-gray-100'" x-text="selected.locked ? 'Terkunci' : 'Kunci'"></button>
                                    <button type="button" x-show="selected.type !== 'button'" x-on:click="selected.hidden = !selected.hidden; commit()" class="rounded bg-gray-100 px-2 py-0.5 text-xs" x-text="selected.hidden ? 'Tampilkan' : 'Sembunyikan'"></button>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-1.5">
                                <template x-for="f in ['x', 'y', 'w', 'h']" :key="f">
                                    <label class="text-xs text-gray-500"><span x-text="f.toUpperCase()"></span>
                                        <input type="number" class="mt-0.5 w-full rounded-md border-gray-200 px-1.5 py-1 text-sm" x-model.number="selected[f]" x-on:change="commit()">
                                    </label>
                                </template>
                            </div>
                            <label class="block text-xs text-gray-500">Putar <span x-text="selected.r + '°'"></span>
                                <input type="range" min="-180" max="180" class="w-full" x-model.number="selected.r" x-on:change="commit()">
                            </label>
                            <div class="flex gap-1.5">
                                <button type="button" x-on:click="center('x')" class="flex-1 rounded-md bg-gray-100 py-1 text-xs">Tengah horizontal</button>
                                <button type="button" x-on:click="center('y')" class="flex-1 rounded-md bg-gray-100 py-1 text-xs">Tengah vertikal</button>
                            </div>

                            {{-- Text --}}
                            <template x-if="selected.type === 'text'">
                                <div class="space-y-3 border-t border-gray-100 pt-3">
                                    <textarea rows="3" maxlength="300" class="form-input text-sm" x-model="selected.props.text" x-on:change="commit()"></textarea>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="ph in ['{groom}', '{bride}', '{date}', '{guest}']" :key="ph">
                                            <button type="button" class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[11px]" x-on:click="selected.props.text += ph; commit()" x-text="ph"></button>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-2 gap-1.5">
                                        <select class="rounded-md border-gray-200 py-1 text-sm" x-model="selected.props.font" x-on:change="commit()">
                                            <option value="heading">Judul</option><option value="script">Sambung</option><option value="body">Isi</option>
                                        </select>
                                        <select class="rounded-md border-gray-200 py-1 text-sm" x-model.number="selected.props.weight" x-on:change="commit()">
                                            <option value="300">Tipis</option><option value="400">Normal</option><option value="500">Sedang</option><option value="600">Tebal</option><option value="700">Sangat tebal</option>
                                        </select>
                                    </div>
                                    <label class="block text-xs text-gray-500">Ukuran <span x-text="selected.props.size"></span>
                                        <input type="range" min="8" max="120" class="w-full" x-model.number="selected.props.size" x-on:change="commit()">
                                    </label>
                                    <label class="block text-xs text-gray-500">Jarak huruf
                                        <input type="range" min="-0.05" max="0.6" step="0.01" class="w-full" x-model.number="selected.props.spacing" x-on:change="commit()">
                                    </label>
                                    <div class="flex gap-1">
                                        <template x-for="a in ['left', 'center', 'right']" :key="a">
                                            <button type="button" class="flex-1 rounded-md py-1 text-xs" :class="selected.props.align === a ? 'bg-sky-100 text-sky-800' : 'bg-gray-100'" x-on:click="selected.props.align = a; commit()" x-text="{ left: 'Kiri', center: 'Tengah', right: 'Kanan' }[a]"></button>
                                        </template>
                                        <button type="button" class="flex-1 rounded-md py-1 text-xs italic" :class="selected.props.italic ? 'bg-sky-100 text-sky-800' : 'bg-gray-100'" x-on:click="selected.props.italic = !selected.props.italic; commit()">Miring</button>
                                    </div>
                                    @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Warna', 'model' => 'selected.props.color'])
                                </div>
                            </template>

                            {{-- Image --}}
                            <template x-if="selected.type === 'image'">
                                <div class="space-y-3 border-t border-gray-100 pt-3">
                                    <div class="flex gap-1">
                                        <button type="button" class="flex-1 rounded-md py-1 text-xs" :class="selected.props.fit === 'cover' ? 'bg-sky-100 text-sky-800' : 'bg-gray-100'" x-on:click="selected.props.fit = 'cover'; commit()">Penuhi</button>
                                        <button type="button" class="flex-1 rounded-md py-1 text-xs" :class="selected.props.fit === 'contain' ? 'bg-sky-100 text-sky-800' : 'bg-gray-100'" x-on:click="selected.props.fit = 'contain'; commit()">Utuh</button>
                                    </div>
                                    <label class="block text-xs text-gray-500">Sudut membulat <input type="range" min="0" max="200" class="w-full" x-model.number="selected.props.radius" x-on:change="commit()"></label>
                                    <label class="block text-xs text-gray-500">Bingkai <input type="range" min="0" max="12" class="w-full" x-model.number="selected.props.border" x-on:change="commit()"></label>
                                    @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Warna bingkai', 'model' => 'selected.props.borderColor'])
                                    <label class="block text-xs text-gray-500">Transparansi <input type="range" min="0" max="1" step="0.05" class="w-full" x-model.number="selected.props.opacity" x-on:change="commit()"></label>
                                </div>
                            </template>

                            {{-- Sticker --}}
                            <template x-if="selected.type === 'sticker'">
                                <div class="space-y-3 border-t border-gray-100 pt-3">
                                    <div class="flex gap-1">
                                        <button type="button" class="flex-1 rounded-md bg-gray-100 py-1 text-xs" x-on:click="selected.props.flipX = !selected.props.flipX; commit()">Balik kiri-kanan</button>
                                        <button type="button" class="flex-1 rounded-md bg-gray-100 py-1 text-xs" x-on:click="selected.props.flipY = !selected.props.flipY; commit()">Balik atas-bawah</button>
                                    </div>
                                    <label class="block text-xs text-gray-500">Transparansi <input type="range" min="0" max="1" step="0.05" class="w-full" x-model.number="selected.props.opacity" x-on:change="commit()"></label>
                                    <p class="text-xs text-gray-500">Warna stiker mengikuti warna tema dan varian.</p>
                                </div>
                            </template>

                            {{-- Shape --}}
                            <template x-if="selected.type === 'shape'">
                                <div class="space-y-3 border-t border-gray-100 pt-3">
                                    <template x-if="selected.props.shape !== 'line'">
                                        @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Isi', 'model' => 'selected.props.fill', 'none' => true])
                                    </template>
                                    @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Garis', 'model' => 'selected.props.stroke', 'none' => true])
                                    <label class="block text-xs text-gray-500">Tebal garis <input type="range" min="0" max="12" class="w-full" x-model.number="selected.props.strokeWidth" x-on:change="commit()"></label>
                                    <label class="block text-xs text-gray-500" x-show="selected.props.shape === 'rect'">Sudut membulat <input type="range" min="0" max="120" class="w-full" x-model.number="selected.props.radius" x-on:change="commit()"></label>
                                    <label class="block text-xs text-gray-500">Transparansi <input type="range" min="0" max="1" step="0.05" class="w-full" x-model.number="selected.props.opacity" x-on:change="commit()"></label>
                                </div>
                            </template>

                            {{-- Button --}}
                            <template x-if="selected.type === 'button'">
                                <div class="space-y-3 border-t border-gray-100 pt-3">
                                    <input type="text" maxlength="40" class="form-input text-sm" x-model="selected.props.text" x-on:change="commit()">
                                    @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Warna tombol', 'model' => 'selected.props.color'])
                                    @include('livewire.invitation.editor.partials.canvas-color', ['label' => 'Warna teks', 'model' => 'selected.props.textColor'])
                                    <label class="block text-xs text-gray-500">Ukuran teks <input type="range" min="8" max="28" class="w-full" x-model.number="selected.props.size" x-on:change="commit()"></label>
                                    <label class="block text-xs text-gray-500">Sudut membulat <input type="range" min="0" max="60" class="w-full" x-model.number="selected.props.radius" x-on:change="commit()"></label>
                                    <p class="text-xs text-gray-500">Tombol ini membuka undangan dan wajib ada.</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="border-t border-gray-100 pt-3">
                        <p class="mb-2 font-semibold">Layer</p>
                        <ul class="max-h-64 space-y-1 overflow-y-auto">
                            <template x-for="el in [...state.elements].reverse()" :key="el.id">
                                <li class="flex items-center gap-1 rounded-md px-2 py-1" :class="selectedId === el.id ? 'bg-sky-50 text-sky-900' : 'hover:bg-gray-50'">
                                    <button type="button" class="min-w-0 flex-1 truncate text-left text-xs" :class="el.hidden && 'opacity-40'" x-on:click="selectedId = el.id" x-text="label(el)"></button>
                                    <span x-show="el.locked" class="text-[10px] text-amber-700">kunci</span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    @endif
</div>

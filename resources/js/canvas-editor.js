/*
 * Drag-and-drop designer (ADR 012). The document holds the cover, free pages,
 * the section order and hidden sections. State is kept here; the Livewire
 * component only saves sanitized snapshots. Coordinates are artboard units
 * (width 390; cover height 844, page height configurable), converted from
 * screen pixels with the current display scale.
 */
const W = 390;
const H = 844;
const SNAP = 5;
const HISTORY = 60;

const uid = () => Math.random().toString(36).slice(2, 10);
const clone = (v) => JSON.parse(JSON.stringify(v));
const TOKENS = ['primary', 'accent', 'ink', 'on', 'surface', 'soft', 'bg'];
const css = (c) => (TOKENS.includes(c) ? `var(--color-t-${c})` : c === 'none' ? 'transparent' : c);
const cqw = (n) => `${(n / W) * 100}cqw`;

document.addEventListener('alpine:init', () => {
    window.Alpine.data('canvasEditor', (initial, library, images, context, sections) => ({
        doc: clone(initial),
        pageId: 'cover',
        zoom: 'fit',
        dragIndex: null,
        library,
        images,
        context,
        sections,
        selectedId: null,
        history: [],
        future: [],
        savedSnapshot: '',
        saving: false,
        guides: [],
        stickerSet: 'corner',
        action: null,
        tokens: TOKENS,

        init() {
            this.history = [JSON.stringify(this.doc)];
            this.savedSnapshot = this.history[0];
            window.addEventListener('beforeunload', (e) => { if (this.dirty) { e.preventDefault(); e.returnValue = ''; } });
        },

        // ----- helpers -----
        get dirty() { return JSON.stringify(this.doc) !== this.savedSnapshot; },
        /** The page being edited: the cover or a free page. */
        get state() { return this.pageId === 'cover' ? this.doc.cover : (this.doc.pages.find((p) => p.id === this.pageId) ?? this.doc.cover); },
        get boardH() { return this.pageId === 'cover' ? H : this.state.height; },
        boardStyle() {
            const h = this.boardH;
            const fit = this.pageId === 'cover' ? `min(100%, calc((100vh - 240px) * ${W} / ${H}))` : 'min(100%, 430px)';
            return { backgroundColor: css(this.state.bg), aspectRatio: `${W} / ${h}`, width: this.zoom === 'fit' ? fit : `${W * this.zoom}px` };
        },
        get selected() { return this.state.elements.find((e) => e.id === this.selectedId) ?? null; },
        get scale() { return this.$refs.board ? this.$refs.board.clientWidth / W : 1; },
        resolve(text) {
            return String(text ?? '')
                .replaceAll('{groom}', this.context.groom).replaceAll('{bride}', this.context.bride)
                .replaceAll('{date}', this.context.date).replaceAll('{guest}', this.context.guest);
        },
        box(e) {
            const h = this.boardH;
            return `left:${(e.x / W) * 100}%;top:${(e.y / h) * 100}%;width:${(e.w / W) * 100}%;height:${(e.h / h) * 100}%;transform:rotate(${e.r}deg)`;
        },
        textStyle(p) {
            return `color:${css(p.color)};font-size:${cqw(p.size)};text-align:${p.align};font-weight:${p.weight};letter-spacing:${p.spacing}em;font-style:${p.italic ? 'italic' : 'normal'}`;
        },
        fontClass(p) { return { heading: 'font-heading', script: 'font-script', body: 'font-sans' }[p.font] ?? 'font-sans'; },
        imageStyle(p) {
            return `object-fit:${p.fit};border-radius:${cqw(p.radius)};opacity:${p.opacity};border:${cqw(p.border)} solid ${css(p.borderColor)}`;
        },
        shapeStyle(p) {
            const radius = p.shape === 'circle' ? '9999px' : p.shape === 'arch' ? '999px 999px 0 0' : cqw(p.radius);
            if (p.shape === 'line') return `background-color:${css(p.stroke)};opacity:${p.opacity}`;
            return `background-color:${css(p.fill)};opacity:${p.opacity};border:${cqw(p.strokeWidth)} solid ${css(p.stroke)};border-radius:${radius}`;
        },
        buttonStyle(p) {
            return `background-color:${css(p.color)};color:${css(p.textColor)};font-size:${cqw(p.size)};border-radius:${cqw(p.radius)}`;
        },
        stickerStyle(p) { return `opacity:${p.opacity};transform:scale(${p.flipX ? -1 : 1},${p.flipY ? -1 : 1})`; },
        swatch(c) { return css(c); },

        // ----- history -----
        commit() {
            const snap = JSON.stringify(this.doc);
            if (this.history[this.history.length - 1] === snap) return;
            this.history.push(snap);
            if (this.history.length > HISTORY) this.history.shift();
            this.future = [];
        },
        undo() {
            if (this.history.length < 2) return;
            this.future.push(this.history.pop());
            this.doc = JSON.parse(this.history[this.history.length - 1]);
            this.fixPage();
        },
        redo() {
            if (!this.future.length) return;
            const snap = this.future.pop();
            this.history.push(snap);
            this.doc = JSON.parse(snap);
            this.fixPage();
        },
        fixPage() {
            if (this.pageId !== 'cover' && !this.doc.pages.some((p) => p.id === this.pageId)) this.pageId = 'cover';
            if (!this.selected) this.selectedId = null;
        },

        // ----- elements -----
        add(type, props, size = {}) {
            const w = size.w ?? 200;
            const h = size.h ?? 60;
            const el = { id: uid(), type, x: (W - w) / 2, y: size.y ?? Math.max(0, (this.boardH - h) / 2), w, h, r: 0, hidden: false, locked: false, props };
            this.state.elements.push(el);
            this.selectedId = el.id;
            this.commit();
        },
        addText(kind) {
            const presets = {
                title: [{ text: '{groom} & {bride}', font: 'script', size: 40, color: 'primary', align: 'center', weight: 400, spacing: 0, italic: false }, { w: 340, h: 64 }],
                heading: [{ text: 'Judul', font: 'heading', size: 26, color: 'primary', align: 'center', weight: 600, spacing: 0, italic: false }, { w: 300, h: 40 }],
                body: [{ text: 'Tulis teks di sini', font: 'body', size: 14, color: 'ink', align: 'center', weight: 400, spacing: 0, italic: false }, { w: 280, h: 40 }],
                date: [{ text: '{date}', font: 'body', size: 14, color: 'ink', align: 'center', weight: 500, spacing: 0.1, italic: false }, { w: 280, h: 24 }],
                guest: [{ text: 'Kepada Yth.\n{guest}', font: 'heading', size: 18, color: 'ink', align: 'center', weight: 600, spacing: 0, italic: false }, { w: 300, h: 50 }],
            };
            const [props, size] = presets[kind];
            this.add('text', props, size);
        },
        addSticker(set, key) {
            const sizes = { corner: { w: 130, h: 130 }, crown: { w: 220, h: 64 }, divider: { w: 224, h: 32 }, scene: { w: 390, h: 195 } };
            if (set === 'scene') sizes.scene.y = Math.max(0, this.boardH - 195);
            this.add('sticker', { set, key, opacity: 1, flipX: false, flipY: false }, sizes[set]);
        },
        addShape(shape) {
            const size = shape === 'line' ? { w: 200, h: 2 } : shape === 'circle' ? { w: 160, h: 160 } : shape === 'arch' ? { w: 180, h: 240 } : { w: 220, h: 140 };
            this.add('shape', { shape, fill: 'soft', stroke: 'accent', strokeWidth: shape === 'line' ? 0 : 1, radius: 12, opacity: 1 }, size);
        },
        async addPhoto(event) {
            const file = event.target.files?.[0];
            event.target.value = '';
            if (!file) return;
            this.saving = true;
            this.$wire.upload('photo', file, async () => {
                try {
                    const result = await this.$wire.storePhoto();
                    this.images[result.path] = result.url;
                    this.add('image', { path: result.path, fit: 'cover', radius: 0, opacity: 1, border: 0, borderColor: 'surface' }, { w: 220, h: 290 });
                } finally { this.saving = false; }
            }, () => { this.saving = false; });
        },
        remove() {
            const el = this.selected;
            if (!el) return;
            if (this.pageId === 'cover' && el.type === 'button' && this.state.elements.filter((e) => e.type === 'button').length === 1) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Tombol "Buka Undangan" wajib ada.' } }));
                return;
            }
            this.state.elements = this.state.elements.filter((e) => e.id !== el.id);
            this.selectedId = null;
            this.commit();
        },
        duplicate() {
            const el = this.selected;
            if (!el) return;
            const copy = { ...clone(el), id: uid(), x: el.x + 12, y: el.y + 12, locked: false };
            this.state.elements.push(copy);
            this.selectedId = copy.id;
            this.commit();
        },
        move(delta) {
            const i = this.state.elements.findIndex((e) => e.id === this.selectedId);
            const j = i + delta;
            if (i < 0 || j < 0 || j >= this.state.elements.length) return;
            const list = this.state.elements;
            [list[i], list[j]] = [list[j], list[i]];
            this.commit();
        },
        center(axis) {
            const el = this.selected;
            if (!el) return;
            if (axis === 'x') el.x = (W - el.w) / 2; else el.y = (this.boardH - el.h) / 2;
            this.commit();
        },
        async useTemplate() {
            if (!confirm('Ganti desain sampul dengan desain bawaan tema? Desain sekarang bisa dikembalikan dengan Undo selama halaman belum ditutup.')) return;
            const enabled = this.doc.cover.enabled;
            this.doc.cover = await this.$wire.template();
            this.doc.cover.enabled = enabled;
            this.pageId = 'cover';
            this.selectedId = null;
            this.commit();
        },
        async save() {
            this.saving = true;
            try {
                this.doc = await this.$wire.save(clone(this.doc));
                this.fixPage();
                this.savedSnapshot = JSON.stringify(this.doc);
                this.history.push(this.savedSnapshot);
            } finally { this.saving = false; }
        },

        // ----- pointer interactions -----
        point(event) { return { x: event.clientX, y: event.clientY }; },
        begin(event, el, mode, handle = null) {
            if (el.locked) { this.selectedId = el.id; return; }
            event.preventDefault();
            event.stopPropagation();
            this.selectedId = el.id;
            const rect = this.$refs.board.getBoundingClientRect();
            this.action = {
                mode, handle, start: this.point(event), orig: { x: el.x, y: el.y, w: el.w, h: el.h, r: el.r },
                center: { x: rect.left + (el.x + el.w / 2) * this.scale, y: rect.top + (el.y + el.h / 2) * this.scale },
                keepRatio: el.type === 'sticker' || el.type === 'image',
            };
            const onMove = (e) => this.drag(e, el);
            const onUp = () => {
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onUp);
                this.action = null;
                this.guides = [];
                this.commit();
            };
            window.addEventListener('pointermove', onMove);
            window.addEventListener('pointerup', onUp);
        },
        drag(event, el) {
            const a = this.action;
            if (!a) return;
            const dx = (event.clientX - a.start.x) / this.scale;
            const dy = (event.clientY - a.start.y) / this.scale;

            if (a.mode === 'move') {
                let x = a.orig.x + dx;
                let y = a.orig.y + dy;
                [x, y] = this.snap(el, x, y);
                el.x = Math.round(x);
                el.y = Math.round(y);
            } else if (a.mode === 'resize') {
                let { x, y, w, h } = a.orig;
                const hd = a.handle;
                if (hd.includes('e')) w = a.orig.w + dx;
                if (hd.includes('s')) h = a.orig.h + dy;
                if (hd.includes('w')) { w = a.orig.w - dx; x = a.orig.x + dx; }
                if (hd.includes('n')) { h = a.orig.h - dy; y = a.orig.y + dy; }
                if ((a.keepRatio || event.shiftKey) && hd.length === 2) {
                    const ratio = a.orig.w / a.orig.h;
                    h = w / ratio;
                    if (hd.includes('n')) y = a.orig.y + a.orig.h - h;
                }
                el.w = Math.max(8, Math.round(w));
                el.h = Math.max(4, Math.round(h));
                el.x = Math.round(Math.min(x, a.orig.x + a.orig.w - 8));
                el.y = Math.round(Math.min(y, a.orig.y + a.orig.h - 4));
            } else if (a.mode === 'rotate') {
                let angle = (Math.atan2(event.clientY - a.center.y, event.clientX - a.center.x) * 180) / Math.PI + 90;
                if (event.shiftKey) angle = Math.round(angle / 15) * 15;
                for (const s of [0, 90, 180, -90, 270]) if (Math.abs(angle - s) < 4) angle = s;
                el.r = Math.round(((angle + 540) % 360) - 180);
            }
        },
        snap(el, x, y) {
            const guides = [];
            const xs = [0, W / 2, W];
            const ys = [0, this.boardH / 2, this.boardH];
            for (const o of this.state.elements) {
                if (o.id === el.id || o.hidden) continue;
                xs.push(o.x, o.x + o.w / 2, o.x + o.w);
                ys.push(o.y, o.y + o.h / 2, o.y + o.h);
            }
            const tryAxis = (pos, size, lines, axis) => {
                for (const [offset] of [[0], [size / 2], [size]]) {
                    for (const line of lines) {
                        if (Math.abs(pos + offset - line) < SNAP) {
                            guides.push({ axis, at: line });
                            return line - offset;
                        }
                    }
                }
                return pos;
            };
            x = tryAxis(x, el.w, xs, 'x');
            y = tryAxis(y, el.h, ys, 'y');
            this.guides = guides;
            return [x, y];
        },
        guideStyle(g) { return g.axis === 'x' ? `left:${(g.at / W) * 100}%;top:0;bottom:0;width:1px` : `top:${(g.at / this.boardH) * 100}%;left:0;right:0;height:1px`; },

        // ----- pages & section order -----
        openPage(id) { this.pageId = id; this.selectedId = null; },
        entryLabel(entry) {
            if (entry.startsWith('page:')) return this.doc.pages.find((p) => 'page:' + p.id === entry)?.name ?? 'Halaman';
            return this.sections[entry]?.label ?? entry;
        },
        isHidden(entry) { return this.doc.hidden.includes(entry); },
        toggleHidden(entry) {
            this.doc.hidden = this.isHidden(entry) ? this.doc.hidden.filter((e) => e !== entry) : [...this.doc.hidden, entry];
            this.commit();
        },
        moveEntry(from, to) {
            if (to < 0 || to >= this.doc.order.length || from === to) return;
            const [entry] = this.doc.order.splice(from, 1);
            this.doc.order.splice(to, 0, entry);
            this.commit();
        },
        drop(to) {
            if (this.dragIndex !== null) this.moveEntry(this.dragIndex, to);
            this.dragIndex = null;
        },
        addPage() {
            if (this.doc.pages.length >= 10) { window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Maksimal 10 halaman bebas.' } })); return; }
            const id = uid();
            const page = {
                id, name: `Halaman ${this.doc.pages.length + 1}`, height: 700, bg: 'bg', pattern: false,
                elements: [{ id: uid(), type: 'text', x: 45, y: 300, w: 300, h: 60, r: 0, hidden: false, locked: false,
                    props: { text: 'Tulis sesuatu di sini', font: 'heading', size: 24, color: 'primary', align: 'center', weight: 600, spacing: 0, italic: false } }],
            };
            this.doc.pages.push(page);
            const at = this.doc.order.indexOf('closing');
            this.doc.order.splice(at < 0 ? this.doc.order.length : at, 0, 'page:' + id);
            this.openPage(id);
            this.commit();
        },
        deletePage(id) {
            const page = this.doc.pages.find((p) => p.id === id);
            if (!page || !confirm(`Hapus ${page.name}?`)) return;
            this.doc.pages = this.doc.pages.filter((p) => p.id !== id);
            this.doc.order = this.doc.order.filter((e) => e !== 'page:' + id);
            if (this.pageId === id) this.openPage('cover');
            this.commit();
        },

        // ----- keyboard -----
        key(event) {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(event.target.tagName)) return;
            const mod = event.ctrlKey || event.metaKey;
            if (mod && event.key.toLowerCase() === 'z') { event.preventDefault(); event.shiftKey ? this.redo() : this.undo(); return; }
            if (mod && event.key.toLowerCase() === 'y') { event.preventDefault(); this.redo(); return; }
            if (mod && event.key.toLowerCase() === 'd') { event.preventDefault(); this.duplicate(); return; }
            if (mod && event.key.toLowerCase() === 's') { event.preventDefault(); this.save(); return; }
            const el = this.selected;
            if (!el) return;
            if (event.key === 'Delete' || event.key === 'Backspace') { event.preventDefault(); this.remove(); return; }
            const step = event.shiftKey ? 10 : 1;
            const moves = { ArrowLeft: [-step, 0], ArrowRight: [step, 0], ArrowUp: [0, -step], ArrowDown: [0, step] };
            if (moves[event.key] && !el.locked) {
                event.preventDefault();
                el.x += moves[event.key][0];
                el.y += moves[event.key][1];
                this.commit();
            }
            if (event.key === 'Escape') this.selectedId = null;
        },
        label(e) {
            return {
                text: () => (e.props.text || 'Teks').split('\n')[0].slice(0, 24),
                image: () => 'Foto',
                sticker: () => ({ corner: 'Sudut', crown: 'Mahkota', divider: 'Pembatas', scene: 'Ilustrasi' }[e.props.set] + ' ' + e.props.key),
                shape: () => ({ rect: 'Kotak', circle: 'Lingkaran', arch: 'Lengkung', line: 'Garis' }[e.props.shape]),
                button: () => 'Tombol buka',
            }[e.type]();
        },
    }));
});

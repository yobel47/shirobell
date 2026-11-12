/*
 * Drag-and-drop cover designer (ADR 012). State is kept here; the Livewire
 * component only saves sanitized snapshots. Coordinates are artboard units
 * (390 x 844), converted from screen pixels with the current display scale.
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
    window.Alpine.data('canvasEditor', (initial, library, images, context) => ({
        state: clone(initial),
        library,
        images,
        context,
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
            this.history = [JSON.stringify(this.state)];
            this.savedSnapshot = this.history[0];
            window.addEventListener('beforeunload', (e) => { if (this.dirty) { e.preventDefault(); e.returnValue = ''; } });
        },

        // ----- helpers -----
        get dirty() { return JSON.stringify(this.state) !== this.savedSnapshot; },
        get selected() { return this.state.elements.find((e) => e.id === this.selectedId) ?? null; },
        get scale() { return this.$refs.board ? this.$refs.board.clientWidth / W : 1; },
        resolve(text) {
            return String(text ?? '')
                .replaceAll('{groom}', this.context.groom).replaceAll('{bride}', this.context.bride)
                .replaceAll('{date}', this.context.date).replaceAll('{guest}', this.context.guest);
        },
        box(e) {
            return `left:${(e.x / W) * 100}%;top:${(e.y / H) * 100}%;width:${(e.w / W) * 100}%;height:${(e.h / H) * 100}%;transform:rotate(${e.r}deg)`;
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
            const snap = JSON.stringify(this.state);
            if (this.history[this.history.length - 1] === snap) return;
            this.history.push(snap);
            if (this.history.length > HISTORY) this.history.shift();
            this.future = [];
        },
        undo() {
            if (this.history.length < 2) return;
            this.future.push(this.history.pop());
            this.state = JSON.parse(this.history[this.history.length - 1]);
            if (!this.selected) this.selectedId = null;
        },
        redo() {
            if (!this.future.length) return;
            const snap = this.future.pop();
            this.history.push(snap);
            this.state = JSON.parse(snap);
        },

        // ----- elements -----
        add(type, props, size = {}) {
            const w = size.w ?? 200;
            const h = size.h ?? 60;
            const el = { id: uid(), type, x: (W - w) / 2, y: size.y ?? (H - h) / 2, w, h, r: 0, hidden: false, locked: false, props };
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
            if (el.type === 'button' && this.state.elements.filter((e) => e.type === 'button').length === 1) {
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
            if (axis === 'x') el.x = (W - el.w) / 2; else el.y = (H - el.h) / 2;
            this.commit();
        },
        async useTemplate() {
            if (!confirm('Ganti desain dengan desain bawaan tema? Desain sekarang bisa dikembalikan dengan Undo selama halaman belum ditutup.')) return;
            this.state = await this.$wire.template();
            this.selectedId = null;
            this.commit();
        },
        async save() {
            this.saving = true;
            try {
                this.state = await this.$wire.save(clone(this.state));
                this.savedSnapshot = JSON.stringify(this.state);
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
            const ys = [0, H / 2, H];
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
        guideStyle(g) { return g.axis === 'x' ? `left:${(g.at / W) * 100}%;top:0;bottom:0;width:1px` : `top:${(g.at / H) * 100}%;left:0;right:0;height:1px`; },

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

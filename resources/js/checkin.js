/*
 * QR scanner for the receptionist app (ADR 011). Decodes camera frames on
 * the main thread with jsQR (no worker or blob URLs, so it fits the CSP)
 * and hands each code to the Livewire component.
 */
import jsQR from 'jsqr';

const MAX_WIDTH = 640;
const REPEAT_MS = 4000;

document.addEventListener('alpine:init', () => {
    window.Alpine.data('qrScanner', () => ({
        stream: null,
        active: false,
        error: null,
        last: null,
        lastAt: 0,

        async start() {
            if (!navigator.mediaDevices?.getUserMedia) {
                this.error = 'Browser ini tidak mendukung kamera. Gunakan pencarian nama.';
                return;
            }
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                this.$refs.video.srcObject = this.stream;
                await this.$refs.video.play();
                this.active = true;
                this.tick();
            } catch (e) {
                this.error = 'Kamera tidak bisa dibuka. Izinkan akses kamera di browser, atau gunakan pencarian nama.';
            }
        },

        stop() {
            this.active = false;
            this.stream?.getTracks().forEach((track) => track.stop());
            this.stream = null;
        },

        tick() {
            if (!this.active) return;
            const video = this.$refs.video;
            if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
                const scale = Math.min(1, MAX_WIDTH / video.videoWidth);
                const width = Math.round(video.videoWidth * scale);
                const height = Math.round(video.videoHeight * scale);
                const canvas = this.$refs.canvas;
                canvas.width = width;
                canvas.height = height;
                const context = canvas.getContext('2d', { willReadFrequently: true });
                context.drawImage(video, 0, 0, width, height);
                const code = jsQR(context.getImageData(0, 0, width, height).data, width, height, { inversionAttempts: 'dontInvert' });
                const now = Date.now();
                if (code?.data && (code.data !== this.last || now - this.lastAt > REPEAT_MS)) {
                    this.last = code.data;
                    this.lastAt = now;
                    navigator.vibrate?.(80);
                    this.$wire.scan(code.data);
                }
            }
            requestAnimationFrame(() => this.tick());
        },

        destroy() {
            this.stop();
        },
    }));
});

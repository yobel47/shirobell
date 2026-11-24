#!/usr/bin/env node
/*
 * Capture real catalog thumbnails for every theme and color variant.
 * Writes public/images/themes/{slug}--{variant}.webp (420x525), which
 * components/theme-swatch.blade.php prefers over the generated preview.
 *
 * Run from apps/ while the app is being served:
 *   php artisan serve --port=8123
 *   BASE_URL=http://shirobell.localhost:8123 node scripts/capture-theme-thumbnails.mjs [slug ...]
 *
 * Env: BASE_URL (default http://shirobell.localhost:8000), CHROME (path to Chrome/Chromium).
 * Pass slugs to refresh only those themes.
 */
import { execFileSync, spawn } from 'node:child_process';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';

const base = process.env.BASE_URL ?? 'http://shirobell.localhost:8000';
const only = process.argv.slice(2);
const outDir = path.resolve('public/images/themes');
const chrome = process.env.CHROME ?? [
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    '/usr/bin/google-chrome',
    '/usr/bin/chromium',
].find((p) => fs.existsSync(p));
if (!chrome) throw new Error('Chrome not found; set CHROME=/path/to/chrome');

// Theme/variant list straight from the app.
const php = `$o=[]; foreach (App\\Models\\Theme::query()->available()->get()->filter->hasView() as $t) { $v = array_keys($t->variants()); foreach ($v ?: [''] as $k) { $o[] = [$t->slug, $k]; } } echo json_encode($o);`;
let combos = JSON.parse(execFileSync('php', ['artisan', 'tinker', `--execute=${php}`]).toString().trim().split('\n').pop());
if (only.length) combos = combos.filter(([slug]) => only.includes(slug));
fs.mkdirSync(outDir, { recursive: true });

const port = 9300 + Math.floor(Math.random() * 500);
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'theme-thumbs-'));
const browser = spawn(chrome, ['--headless=new', '--disable-gpu', '--hide-scrollbars', `--remote-debugging-port=${port}`, `--user-data-dir=${profile}`, 'about:blank'], { stdio: 'ignore' });
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

let wsUrl;
for (let i = 0; i < 40 && !wsUrl; i++) {
    await wait(250);
    try {
        const pages = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json();
        wsUrl = pages.find((p) => p.type === 'page')?.webSocketDebuggerUrl;
    } catch {}
}
if (!wsUrl) { browser.kill(); throw new Error('Could not connect to Chrome'); }

const sock = new WebSocket(wsUrl);
await new Promise((r) => sock.addEventListener('open', r, { once: true }));
let id = 0; const pending = new Map();
sock.addEventListener('message', (e) => { const m = JSON.parse(e.data); if (m.id && pending.has(m.id)) { pending.get(m.id)(m.result); pending.delete(m.id); } });
const send = (method, params = {}) => new Promise((res) => { const i = ++id; pending.set(i, res); sock.send(JSON.stringify({ id: i, method, params })); });

await send('Page.enable');
// Reduced motion: covers render in their final state.
await send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
await send('Emulation.setDeviceMetricsOverride', { width: 560, height: 700, deviceScaleFactor: 1, mobile: true });

let n = 0;
for (const [slug, variant] of combos) {
    const q = new URLSearchParams({ frame: '1', ...(variant ? { varian: variant } : {}) });
    await send('Page.navigate', { url: `${base}/tema/${slug}?${q}` });
    await wait(1400);
    await send('Runtime.evaluate', { expression: 'document.fonts.ready.then(() => true)', awaitPromise: true });
    await wait(150);
    const shot = await send('Page.captureScreenshot', { format: 'webp', quality: 80, clip: { x: 0, y: 0, width: 560, height: 700, scale: 0.75 } });
    fs.writeFileSync(path.join(outDir, `${slug}--${variant}.webp`), Buffer.from(shot.data, 'base64'));
    if (++n % 20 === 0) console.log(`${n}/${combos.length}`);
}

console.log(`Captured ${n} thumbnails into ${outDir}`);
sock.close();
// Let Chrome release its profile before removing it (best effort on Windows).
await new Promise((r) => { browser.once('exit', r); browser.kill(); setTimeout(r, 3000); });
try { fs.rmSync(profile, { recursive: true, force: true, maxRetries: 5, retryDelay: 300 }); } catch {}

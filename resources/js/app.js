// Scroll reveal for [data-reveal] elements (landing page). Content stays visible
// without JS or with reduced motion; only below-the-fold elements are hidden.
function initReveal() {
    const els = [...document.querySelectorAll('[data-reveal]')];
    if (!els.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced || !('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-in'));
        return;
    }

    // Already on screen at load: show now, so nothing flashes out and back in.
    els.forEach((el) => {
        if (el.getBoundingClientRect().top < window.innerHeight) el.classList.add('is-in');
    });
    document.documentElement.classList.add('reveal-ready');

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-in');
            io.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -12% 0px' });

    els.filter((el) => !el.classList.contains('is-in')).forEach((el) => io.observe(el));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReveal);
} else {
    initReveal();
}

(function () {
  const STORAGE_KEY = 'ds-theme';
  const root = document.documentElement;

  function getStored() {
    try { return localStorage.getItem(STORAGE_KEY); } catch (_) { return null; }
  }
  function setStored(v) {
    try { localStorage.setItem(STORAGE_KEY, v); } catch (_) {}
  }

  function apply(theme) {
    if (theme === 'dark' || theme === 'light') {
      root.setAttribute('data-theme', theme);
    } else {
      root.removeAttribute('data-theme');
    }
  }

  // Init: prefer stored, else system, else light
  const stored = getStored();
  if (stored === 'dark' || stored === 'light') {
    apply(stored);
  } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    apply('dark');
  } else {
    apply('light');
  }

  // Wire toggle button(s)
  function bind() {
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const current = root.getAttribute('data-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        apply(next);
        setStored(next);
        btn.setAttribute('aria-pressed', String(next === 'dark'));
      });
      const cur = root.getAttribute('data-theme') || 'light';
      btn.setAttribute('aria-pressed', String(cur === 'dark'));
    });

    // Mobile nav toggle
    const navToggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    if (navToggle && nav) {
      navToggle.addEventListener('click', () => {
        const open = nav.classList.toggle('is-open');
        navToggle.setAttribute('aria-expanded', String(open));
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bind);
  } else {
    bind();
  }
})();

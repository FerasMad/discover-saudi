(function () {
  const items = document.querySelectorAll('[data-lightbox-src]');
  if (items.length === 0) return;

  // Build the overlay once, on first open
  let overlay, imgEl, closeBtn;
  function build() {
    overlay = document.createElement('div');
    overlay.className = 'lightbox';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', 'معرض الصور');

    imgEl = document.createElement('img');
    imgEl.alt = '';
    overlay.appendChild(imgEl);

    closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'lightbox-close';
    closeBtn.setAttribute('aria-label', 'إغلاق');
    closeBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M6 18 18 6"/></svg>';
    overlay.appendChild(closeBtn);

    document.body.appendChild(overlay);

    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
  }

  function open(src) {
    if (!overlay) build();
    imgEl.src = src;
    overlay.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    closeBtn.focus();
  }

  function close() {
    if (!overlay) return;
    overlay.classList.remove('is-open');
    document.body.style.overflow = '';
    setTimeout(() => { imgEl.src = ''; }, 250);
  }

  items.forEach((el) => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      open(el.dataset.lightboxSrc);
    });
  });
})();

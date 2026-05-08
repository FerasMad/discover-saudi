(function () {
  const grid    = document.querySelector('[data-gallery-grid]');
  const search  = document.querySelector('[data-gallery-search]');
  const filter  = document.querySelector('[data-gallery-filter]');
  const counter = document.querySelector('[data-gallery-count]');
  const empty   = document.querySelector('[data-gallery-empty]');
  const reset   = document.querySelector('[data-gallery-reset]');

  if (!grid || !search || !filter) return;

  const tiles = Array.from(grid.querySelectorAll('.g-tile'));

  function debounce(fn, ms) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  }

  function apply() {
    const q = (search.value || '').trim().toLowerCase();
    const type = filter.value;
    let visible = 0;

    tiles.forEach((tile) => {
      const name    = tile.dataset.name || '';
      const tagline = tile.dataset.tagline || '';
      const ttype   = tile.dataset.type || '';

      const matchesQuery = !q || name.includes(q) || tagline.includes(q);
      const matchesType  = !type || ttype === type;
      const show = matchesQuery && matchesType;

      tile.classList.toggle('is-hidden', !show);
      if (show) visible++;
    });

    if (counter) counter.textContent = String(visible);
    if (empty) empty.hidden = visible !== 0;
  }

  search.addEventListener('input', debounce(apply, 180));
  filter.addEventListener('change', apply);
  if (reset) {
    reset.addEventListener('click', () => {
      search.value = '';
      filter.value = '';
      apply();
      search.focus();
    });
  }
})();

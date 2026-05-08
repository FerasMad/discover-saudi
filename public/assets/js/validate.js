(function () {
  const form = document.getElementById('place-form');
  if (!form) return;

  // ---- live image preview ----
  document.querySelectorAll('input[type="file"][data-preview-target]').forEach((input) => {
    const target = document.querySelector(input.dataset.previewTarget);
    if (!target) return;
    input.addEventListener('change', () => {
      const f = input.files && input.files[0];
      if (!f) { target.innerHTML = '<span>لم تُختر صورة بعد.</span>'; return; }
      if (!/^image\//.test(f.type)) {
        target.innerHTML = '<span style="color: var(--danger);">الملف ليس صورة.</span>';
        return;
      }
      const reader = new FileReader();
      reader.onload = (e) => {
        target.innerHTML = '';
        const img = document.createElement('img');
        img.src = e.target.result;
        img.alt = '';
        target.appendChild(img);
      };
      reader.readAsDataURL(f);
    });
  });

  // ---- validation ----
  function fieldOf(input) { return input.closest('.field'); }
  function setError(input, ok) {
    const f = fieldOf(input);
    if (f) f.classList.toggle('has-error', !ok);
  }

  function validateField(input) {
    if (input.type === 'file') {
      if (input.required && (!input.files || input.files.length === 0)) {
        setError(input, false);
        return false;
      }
      if (input.files && input.files.length > 0) {
        const f = input.files[0];
        if (!/^image\//.test(f.type)) {
          setError(input, false);
          return false;
        }
      }
      setError(input, true);
      return true;
    }

    const v = (input.value || '').trim();

    if (input.required && v === '') {
      setError(input, false);
      return false;
    }
    if (input.minLength && v.length > 0 && v.length < input.minLength) {
      setError(input, false);
      return false;
    }
    if (input.tagName === 'SELECT' && input.required && v === '') {
      setError(input, false);
      return false;
    }

    setError(input, true);
    return true;
  }

  // Validate on blur/change for instant feedback
  form.querySelectorAll('input, textarea, select').forEach((input) => {
    input.addEventListener('blur', () => {
      if (input.dataset.touched) validateField(input);
    });
    input.addEventListener('input', () => { input.dataset.touched = '1'; });
    input.addEventListener('change', () => { input.dataset.touched = '1'; validateField(input); });
  });

  form.addEventListener('submit', (e) => {
    let ok = true;
    let firstBad = null;
    form.querySelectorAll('input, textarea, select').forEach((input) => {
      const valid = validateField(input);
      if (!valid && !firstBad) firstBad = input;
      ok = ok && valid;
    });
    if (!ok) {
      e.preventDefault();
      if (firstBad) {
        firstBad.focus();
        firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });
})();

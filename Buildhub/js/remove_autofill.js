(function() {
  function wire(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    // Unlock fields only when user interacts; clear any injected values
    form.querySelectorAll('[data-sync]').forEach(el => {
      try { el.value = ''; } catch (_) {}
      el.addEventListener('focus', () => el.removeAttribute('readonly'), { once: true });

      const name = el.getAttribute('data-sync');
      const hidden = form.querySelector('input[type="hidden"][name="' + name + '"]');
      if (!hidden) return;
      const sync = () => hidden.value = el.value;
      el.addEventListener('input', sync);
      el.addEventListener('change', sync);
    });

    // belt & suspenders: final sync on submit
    form.addEventListener('submit', () => {
      form.querySelectorAll('[data-sync]').forEach(el => {
        const name = el.getAttribute('data-sync');
        const hidden = form.querySelector('input[type="hidden"][name="' + name + '"]');
        if (hidden) hidden.value = el.value;
      });
    });
  }

  wire('loginForm');
  wire('registerForm');
})();


(() => {
  const form = document.getElementById('pwForm');
  if (!form) return;

  // Unlock only on user action; clear any injected value
  form.querySelectorAll('[data-sync]').forEach(el => {
    try { el.value = ''; } catch (_) {}
    el.addEventListener('focus', () => el.removeAttribute('readonly'), { once: true });

    const name = el.getAttribute('data-sync');
    const hidden = form.querySelector('input[type="hidden"][name="' + name + '"]');
    if (!hidden) return;

    const sync = () => hidden.value = el.value;
    el.addEventListener('input', sync);
    el.addEventListener('change', sync);
  });

  // Final sync before submit
  form.addEventListener('submit', () => {
    form.querySelectorAll('[data-sync]').forEach(el => {
      const name = el.getAttribute('data-sync');
      const hidden = form.querySelector('input[type="hidden"][name="' + name + '"]');
      if (hidden) hidden.value = el.value;
    });
  });
})();
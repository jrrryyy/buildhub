// book.js
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.book-form').forEach((form) => {
    if (form.dataset.bound) return;
    form.dataset.bound = '1';

    const inc        = form.querySelector('.increaseBtn');
    const dec        = form.querySelector('.decreaseBtn');
    const counter    = form.querySelector('.counter');
    const totalEl    = form.querySelector('.total-value');
    const qtyInput   = form.querySelector('.quantity-input');
    const totalInput = form.querySelector('.total-input');
    const price      = Number(form.querySelector('input[name="price"]')?.value) || 0;

    [inc, dec].forEach(b => b?.setAttribute('type','button'));

    const set = (n) => {
      const total = n * price;
      counter.textContent = n;
      totalEl.textContent = `₱${total.toFixed(2)}`;
      if (qtyInput)   qtyInput.value = n;
      if (totalInput) totalInput.value = total.toFixed(2);
    };

    inc?.addEventListener('click', (e) => { e.preventDefault(); set((+counter.textContent||0) + 1); });
    dec?.addEventListener('click', (e) => { e.preventDefault(); set(Math.max(0, (+counter.textContent||0) - 1)); });

    // 👉 PLACE YOUR SUBMIT LISTENER RIGHT HERE
    form.addEventListener('submit', (e) => {
      const qty = +counter.textContent || 0;
      if (qty <= 0) { e.preventDefault(); alert('Please select at least 1 quantity before booking.'); return; }
      // no window.open — let the form submit to checkout.php
    });

    set(+counter.textContent || 0);
  });
});

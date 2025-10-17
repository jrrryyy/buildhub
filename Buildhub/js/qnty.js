document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.book-form').forEach(form => {
    if (form.dataset.bound) return; form.dataset.bound = '1';

    const qtyField   = form.querySelector('.quantity-field');   // visible <input type="number">
    const qtyHidden  = form.querySelector('.quantity-input');   // hidden input name="quantity"
    const totalHidden= form.querySelector('.total-input');       // hidden input name="total"
    const totalText  = form.querySelector('.total-value');       // "₱0.00" span
    const price      = parseFloat(form.querySelector('input[name="price"]')?.value || '0') || 0;

    function clampInt(v){ v = parseInt(v, 10); return isNaN(v) || v < 0 ? 0 : v; }
    function peso(n){ return '₱' + Number(n).toFixed(2); }

    function update(n){
      const q = clampInt(n);
      const t = q * price;
      qtyField.value    = q;
      qtyHidden.value   = q;
      totalHidden.value = t.toFixed(2);
      if (totalText) totalText.textContent = peso(t);
    }

    // init
    update(qtyField.value || 0);

    // react to typing/clicks on the number input
    qtyField.addEventListener('input', () => update(qtyField.value));

    // guard: block submit if qty is 0
    form.addEventListener('submit', (e) => {
      if (clampInt(qtyField.value) === 0) {
        e.preventDefault();
        alert('Please select a quantity greater than 0.');
        qtyField.focus();
      }
    });
  });
});
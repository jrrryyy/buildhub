document.addEventListener('DOMContentLoaded', () => {
  // Only bind cards that do NOT contain a .book-form
  document.querySelectorAll('.bg-white.rounded-lg.shadow-md').forEach(card => {
    if (card.querySelector('.book-form')) return; // ← skip this card
    if (card.dataset.bound) return; card.dataset.bound = '1';
    const inc = card.querySelector('.increaseBtn');
    const dec = card.querySelector('.decreaseBtn');
    const counter = card.querySelector('.counter');
    const totalEl = card.querySelector('.total-value');
    const price = Number((card.querySelector('.product-price')?.textContent || '0').replace(/[^\d.]/g,'')) || 0;

    const set = (n) => {
      counter.textContent = n;
      totalEl.textContent = `₱${(n*price).toFixed(2)}`;
    };

    inc?.addEventListener('click', () => set((+counter.textContent||0) + 1));
    dec?.addEventListener('click', () => set(Math.max(0, (+counter.textContent||0) - 1)));

    set(+counter.textContent||0);
  });
});

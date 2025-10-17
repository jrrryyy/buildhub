(function () {
  const modal    = document.getElementById('updateModal');
  const content  = document.getElementById('updateModalContent');
  const closeBtn = document.getElementById('updateCloseBtn');

  const updId    = document.getElementById('updId');
  const updName  = document.getElementById('updName');
  const updQty   = document.getElementById('updQty');
  const updPrice = document.getElementById('updPrice');
  const updDesc  = document.getElementById('updDesc');
  const updPrev  = document.getElementById('updPreview');

  const fileInput  = document.getElementById('updImageInput');
  const fileNameEl = document.getElementById('updFileName');

  function openUpdate() {
    modal.classList.remove('opacity-0','pointer-events-none');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
    modal.setAttribute('aria-hidden','false');
  }
  function closeUpdate() {
    modal.classList.add('opacity-0','pointer-events-none');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    modal.setAttribute('aria-hidden','true');
  }

  // Open from any .openUpdateModal button (inside cards)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.openUpdateModal');
    if (!btn) return;

    // Fill form fields from data-* attributes
    updId.value    = btn.dataset.id || '';
    updName.value  = btn.dataset.name || '';
    updQty.value   = btn.dataset.qty || '0';
    updPrice.value = btn.dataset.price || '0';
    updDesc.value  = btn.dataset.desc || '';
    updPrev.src    = btn.dataset.image || '';

    // Reset file UI
    if (fileInput) fileInput.value = '';
    if (fileNameEl) {
      fileNameEl.textContent = 'Upload Photo';
      fileNameEl.classList.remove('text-gray-900','text-gray-500');
      fileNameEl.classList.add('text-gray-400');
    }

    openUpdate();
  });

  // Close actions
  closeBtn?.addEventListener('click', closeUpdate);
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) closeUpdate();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') closeUpdate();
  });

  // Show chosen file name
  fileInput?.addEventListener('change', () => {
    if (!fileInput.files || fileInput.files.length === 0) {
      fileNameEl.textContent = 'No file chosen';
      fileNameEl.classList.remove('text-gray-900');
      fileNameEl.classList.add('text-gray-500');
      return;
    }
    const name = fileInput.files.length === 1
      ? fileInput.files[0].name
      : `${fileInput.files.length} files selected`;
    fileNameEl.textContent = name;
    fileNameEl.classList.remove('text-gray-400','text-gray-500');
    fileNameEl.classList.add('text-gray-900');
  });
})();
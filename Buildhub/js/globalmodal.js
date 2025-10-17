document.addEventListener('DOMContentLoaded', function () {
  /* 1) STRICT: only open update modal when clicking the real Update button */
  const updateModal   = document.getElementById('updateModal');
  const updateContent = document.getElementById('updateModalContent');
  const updId    = document.getElementById('updId');
  const updName  = document.getElementById('updName');
  const updQty   = document.getElementById('updQty');
  const updPrice = document.getElementById('updPrice');
  const updDesc  = document.getElementById('updDesc');
  const updPrev  = document.getElementById('updPreview');
  const updFile  = document.getElementById('updImageInput');
  const updFileName = document.getElementById('updFileName');
  const updateClose = document.getElementById('updateCloseBtn');

  function openUpdate() {
    updateModal.classList.remove('opacity-0','pointer-events-none');
    updateContent.classList.remove('scale-95');
    updateContent.classList.add('scale-100');
    updateModal.setAttribute('aria-hidden','false');
  }
  function closeUpdate() {
    updateModal.classList.add('opacity-0','pointer-events-none');
    updateContent.classList.remove('scale-100');
    updateContent.classList.add('scale-95');
    updateModal.setAttribute('aria-hidden','true');
  }

  // Bind directly to the Update buttons only (no delegation)
  document.querySelectorAll('button.openUpdateModal[data-id]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation(); // never let this bubble to any generic modal handlers

      // Fill fields from data-*
      if (updId)    updId.value    = btn.dataset.id || '';
      if (updName)  updName.value  = btn.dataset.name || '';
      if (updQty)   updQty.value   = btn.dataset.qty || '0';
      if (updPrice) updPrice.value = btn.dataset.price || '0';
      if (updDesc)  updDesc.value  = btn.dataset.desc || '';
      if (updPrev)  updPrev.src    = btn.dataset.image || '';

      // Reset file UI
      if (updFile) {
        updFile.value = '';
        if (updFileName) {
          updFileName.textContent = 'Upload Photo';
          updFileName.classList.remove('text-gray-900','text-gray-500');
          updFileName.classList.add('text-gray-400');
        }
      }

      openUpdate();
    });
  });

  // Close controls
  updateModal?.addEventListener('click', function (e) {
    if (e.target === updateModal) closeUpdate();
  });
  updateClose?.addEventListener('click', closeUpdate);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && updateModal.getAttribute('aria-hidden') === 'false') closeUpdate();
  });
  updFile?.addEventListener('change', function () {
    if (!updFile.files || updFile.files.length === 0) {
      if (updFileName) {
        updFileName.textContent = 'No file chosen';
        updFileName.classList.remove('text-gray-900');
        updFileName.classList.add('text-gray-500');
      }
      return;
    }
    const name = updFile.files.length === 1 ? updFile.files[0].name : `${updFile.files.length} files selected`;
    if (updFileName) {
      updFileName.textContent = name;
      updFileName.classList.remove('text-gray-400','text-gray-500');
      updFileName.classList.add('text-gray-900');
    }
  });

  /* 2) HARD BLOCK: ensure Delete never triggers any modal handler */
  const deleteButtons = document.querySelectorAll('form[action="../admin/crud.php"] button[name="delete"]');
  ['click','mousedown','mouseup','touchstart','touchend'].forEach(function (type) {
    deleteButtons.forEach(function (btn) {
      // capture phase + stopImmediatePropagation to beat delegated listeners from other scripts
      btn.addEventListener(type, function (e) {
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      }, true);
    });
  });
});
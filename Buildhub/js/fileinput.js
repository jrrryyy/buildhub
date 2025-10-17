  (function () {
    const input = document.getElementById('imageInput');
    const fileNameEl = document.getElementById('fileName');

    function showName() {
      if (!input.files || input.files.length === 0) {
        fileNameEl.textContent = 'No file chosen';
        fileNameEl.classList.remove('text-gray-900');
        fileNameEl.classList.add('text-gray-500');
        return;
      }
      const name = input.files.length === 1
        ? input.files[0].name
        : `${input.files.length} files selected`;
      fileNameEl.textContent = name;
      fileNameEl.classList.remove('text-gray-500');
      fileNameEl.classList.add('text-gray-900');
    }

    input.addEventListener('change', showName);
  })();
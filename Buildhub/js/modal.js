document.addEventListener("DOMContentLoaded", () => {
  // Sidebar logic (unchanged) ...
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const toggles = document.querySelectorAll("[data-sidebar-toggle]");

  function openSidebar() {
    sidebar?.classList.remove("-translate-x-full");
    overlay?.classList.remove("hidden");
  }
  function closeSidebar() {
    sidebar?.classList.add("-translate-x-full");
    overlay?.classList.add("hidden");
  }
  toggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (sidebar?.classList.contains("-translate-x-full")) openSidebar();
      else closeSidebar();
    });
  });
  overlay?.addEventListener("click", closeSidebar);
  const mq = window.matchMedia("(min-width: 768px)");
  mq.addEventListener?.("change", (e) => { if (e.matches) closeSidebar(); });

  // --- MODAL LOGIC ---
  const addBtn       = document.getElementById("addProductBtn");
  const modal        = document.getElementById("modal");
  const modalContent = document.getElementById("modalContent");
  const errorBox     = document.getElementById("crudErrorBox");

  if (!addBtn || !modal) {
    console.warn("⚠️ Modal or Add button not found in DOM.");
    return;
  }

  function openModal() {
    modal.classList.remove("opacity-0", "pointer-events-none");
    modalContent?.classList.remove("scale-95");
    modalContent?.classList.add("scale-100");
  }
  function closeModal() {
    modal.classList.add("opacity-0", "pointer-events-none");
    modalContent?.classList.remove("scale-100");
    modalContent?.classList.add("scale-95");
  }

  addBtn.addEventListener("click", () => {
    // toggle open/close
    if (modal.classList.contains("opacity-0")) openModal();
    else closeModal();
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  // --- FLASH FROM SERVER ---
  const flash = (window.__FLASH__ || {});
  if (flash.error) {
    // show error text
    if (errorBox) {
      errorBox.textContent = flash.error;
      errorBox.classList.remove("hidden");
    }
    // auto-open the modal
    openModal();
  }
  // Example if you also want to show success (outside modal) you can handle here
  // if (flash.success) { ... }
});




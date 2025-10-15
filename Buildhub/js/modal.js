document.addEventListener("DOMContentLoaded", () => {
  // Sidebar logic (only runs if elements exist)
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
  mq.addEventListener?.("change", (e) => {
    if (e.matches) closeSidebar();
  });

  // --- MODAL LOGIC ---
  const addBtn = document.getElementById("addProductBtn");
  const modal = document.getElementById("modal");

  if (!addBtn || !modal) {
    console.warn("⚠️ Modal or Add button not found in DOM.");
    return;
  }

  addBtn.addEventListener("click", () => {
    modal.classList.toggle("opacity-0");
    modal.classList.toggle("pointer-events-none");
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.classList.add("opacity-0", "pointer-events-none");
    }
  });
});

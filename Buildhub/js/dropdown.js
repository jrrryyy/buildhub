 const profileButton = document.getElementById("profileButton");
    const profileDropdown = document.getElementById("profileDropdown");

    // Toggle dropdown visibility
    profileButton.addEventListener("click", () => {
      profileDropdown.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    window.addEventListener("click", (e) => {
      if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.classList.add("hidden");
      }
    });

    function showSupportInfo() {
      alert(
        "About this System\n\nSystem Name: BuildHub Template\nDeveloper: Your Team Name\nContact No.: +63 900 000 0000\nEmail: support@buildhub.io\n\nCopyright © 2025 BuildHub."
      );
    }
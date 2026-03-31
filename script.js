
 
  /* ── DRAWER ── */
  function toggleDrawer() {
    const drawer    = document.getElementById("drawer");
    const overlay   = document.getElementById("overlay");
    const hamburger = document.getElementById("hamburger");
    const isOpen    = drawer.classList.contains("open");
    if (isOpen) closeDrawer(); else openDrawer();
  }
 
  function openDrawer() {
    document.getElementById("drawer").classList.add("open");
    document.getElementById("overlay").classList.add("show");
    document.getElementById("hamburger").classList.add("active");
  }
 
  function closeDrawer() {
    document.getElementById("drawer").classList.remove("open");
    document.getElementById("overlay").classList.remove("show");
    document.getElementById("hamburger").classList.remove("active");
  }
 
  /* ── INIT ── */
  render();
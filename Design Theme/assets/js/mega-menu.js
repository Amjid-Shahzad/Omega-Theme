document.addEventListener("DOMContentLoaded", () => {
  /**
   * 1️⃣  Add correct classes to menu items / submenus
   */
  document.querySelectorAll("li.menu-item > a[href^='#']").forEach(link => {
    const li = link.closest("li.menu-item");
    const slug = link.getAttribute("href").substring(1);
    if (!li || !slug) return;

    const subMenu = li.closest(".sub-menu");

    // Top-level mega menu → slug first, then has-mega-menu
    if (!subMenu) {
      li.classList.add(slug, "has-mega-menu");
    } 
    // Submenu-level mega menu → class goes on <ul>, slug-first naming
    else {
      subMenu.classList.add("sub-mega-menu", slug + "-mega-menu");
    }
  });


 // 1️⃣ Select all list items that contain a mega-menu dropdown
  const menuItems = document.querySelectorAll('.menu-item.sub-mega-menu');

  menuItems.forEach(liItem => {
    // 2️⃣ Find the dropdown inside this <li>
    const dropdown = liItem.querySelector('.mega-menu-dropdown');

    // 3️⃣ Remove the <a> tag (so "shop" or similar text doesn’t show)
    const link = liItem.querySelector('a');
    if (link) link.remove(); // completely removes <a>

    // 4️⃣ If the dropdown exists, move it out of the <li> and remove the <li> wrapper
    if (dropdown) {
      liItem.parentNode.insertBefore(dropdown, liItem);
      liItem.remove();
    }
  });




});


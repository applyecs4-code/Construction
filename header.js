// ===================== COMMON.JS =====================

// ✅ Load Header & Footer automatically on every page
document.addEventListener("DOMContentLoaded", async () => {
  try {
    // --- Load Header ---
    const headerContainer = document.createElement("div");
    headerContainer.id = "header-container";
    document.body.prepend(headerContainer);

    const headerRes = await fetch("header.html");
    if (!headerRes.ok) throw new Error("Header file not found");
    const headerHTML = await headerRes.text();
    headerContainer.innerHTML = headerHTML;

    // Initialize header scripts immediately after inject
    initScrollEffect();
    initMobileMenu();
    initAccordionDropdowns();
    setActiveMenuItem();
    initKeyboardNavigation();

    // --- Load Footer ---
    const footerContainer = document.createElement("div");
    footerContainer.id = "footer-container";
    document.body.append(footerContainer);

    const footerRes = await fetch("footer.html");
    if (!footerRes.ok) throw new Error("Footer file not found");
    const footerHTML = await footerRes.text();
    footerContainer.innerHTML = footerHTML;

    // Page-specific scripts (may not exist on all pages - safe to init)
    initReviewSlider();
    initFAQAccordion();

  } catch (e) {
    console.error("❌ Header/Footer load failed:", e);
  }
});

// ===================== SCROLL EFFECT =====================

function initScrollEffect() {
  const header = document.querySelector("header, .header");
  if (!header) return;

  function checkScroll() {
    if (window.scrollY > 10) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  }

  window.addEventListener("scroll", checkScroll, { passive: true });
  checkScroll(); // run on load
}

// ===================== MOBILE MENU =====================

function initMobileMenu() {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");

  if (!hamburger || !navMenu) {
    console.warn("Mobile menu elements not found");
    return;
  }

  // Create overlay if missing
  let overlay = document.querySelector(".mobile-overlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.className = "mobile-overlay";
    document.body.appendChild(overlay);
  }

  function openMenu() {
    hamburger.classList.add("active");
    navMenu.classList.add("active");
    overlay.classList.add("active");
    hamburger.setAttribute("aria-expanded", "true");
    document.body.style.overflow = "hidden";
  }

  function closeMenu() {
    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
    overlay.classList.remove("active");
    hamburger.setAttribute("aria-expanded", "false");
    document.body.style.overflow = "";
    // Close all open dropdowns
    document.querySelectorAll(".has-dropdown.active, .course-dropdown.active").forEach(el => {
      el.classList.remove("active");
    });
  }

  window.closeMobileMenu = closeMenu;

  hamburger.addEventListener("click", (e) => {
    e.stopPropagation();
    navMenu.classList.contains("active") ? closeMenu() : openMenu();
  });

  overlay.addEventListener("click", closeMenu);

  // ESC key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
  });

  // Close menu on resize to desktop
  window.addEventListener("resize", () => {
    if (window.innerWidth > 992) closeMenu();
  });

  console.log("✅ Mobile menu initialized");
}

// ===================== ACCORDION DROPDOWNS (MOBILE) =====================

function initAccordionDropdowns() {
  // Event delegation - works even with dynamically injected headers
  document.addEventListener("click", (e) => {
    // Only on mobile
    if (window.innerWidth > 992) return;

    // Check if click is on a dropdown parent link (has-dropdown or course-dropdown)
    const toggleLink = e.target.closest(".has-dropdown > a, .course-dropdown > a");
    if (!toggleLink) return;

    e.preventDefault();
    e.stopPropagation();

    const parent = toggleLink.closest(".has-dropdown, .course-dropdown");
    if (!parent) return;

    const isActive = parent.classList.contains("active");

    // Close all dropdowns
    document.querySelectorAll(".has-dropdown.active, .course-dropdown.active").forEach(el => {
      el.classList.remove("active");
    });

    // Toggle current (open if was closed)
    if (!isActive) {
      parent.classList.add("active");
    }
  });

  // Simple nav links (non-dropdown) - close mobile menu on click
  document.addEventListener("click", (e) => {
    if (window.innerWidth > 992) return;

    const link = e.target.closest(".nav-menu a");
    if (!link) return;

    // Skip if it's a dropdown parent toggle
    if (link.closest(".has-dropdown") && link === link.closest(".has-dropdown").querySelector(":scope > a")) return;
    if (link.closest(".course-dropdown") && link === link.closest(".course-dropdown").querySelector(":scope > a")) return;

    // It's a real destination link - close menu
    setTimeout(() => {
      if (window.closeMobileMenu) window.closeMobileMenu();
    }, 120);
  });

  console.log("✅ Accordion dropdowns initialized");
}

// ===================== ACTIVE PAGE HIGHLIGHT =====================

function setActiveMenuItem() {
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  document.querySelectorAll(".nav-menu a").forEach((item) => {
    const href = item.getAttribute("href");
    if (href === currentPage || (currentPage === "" && href === "index.html")) {
      item.classList.add("active");
    } else {
      item.classList.remove("active");
    }
  });
}

// ===================== KEYBOARD NAVIGATION =====================

function initKeyboardNavigation() {
  // Close desktop dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    if (window.innerWidth <= 992) return;
    if (!e.target.closest(".has-dropdown") && !e.target.closest(".course-dropdown")) {
      document.querySelectorAll(".has-dropdown.active, .course-dropdown.active").forEach(el => {
        el.classList.remove("active");
      });
    }
  });
}

// ===================== REVIEW SLIDER =====================

function initReviewSlider() {
  const wrapper = document.getElementById("reviewWrapper");
  const dots = document.querySelectorAll(".dot");
  const totalSlides = dots.length;

  if (!wrapper || totalSlides === 0) return;

  let currentSlide = 0;

  function moveToSlide(index) {
    currentSlide = index;
    wrapper.style.transform = `translateX(-${index * 100}%)`;
    dots.forEach((dot) => dot.classList.remove("active"));
    if (dots[index]) dots[index].classList.add("active");
  }

  const slideInterval = setInterval(() => {
    currentSlide = (currentSlide + 1) % totalSlides;
    moveToSlide(currentSlide);
  }, 5000);

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => moveToSlide(index));
  });

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) clearInterval(slideInterval);
  });
}

// ===================== FAQ ACCORDION =====================

function initFAQAccordion() {
  const faqItems = document.querySelectorAll(".faq-item");
  if (faqItems.length === 0) return;

  faqItems.forEach((item) => {
    item.classList.remove("active");
    const answer = item.querySelector(".faq-answer, .faq-content");
    if (answer) answer.style.display = "none";
  });

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");
    const answer = item.querySelector(".faq-answer, .faq-content");
    if (!question || !answer) return;

    question.addEventListener("click", () => {
      const isActive = item.classList.contains("active");
      item.classList.toggle("active");
      answer.style.display = isActive ? "none" : "block";
    });
  });
}

// ===================== ERROR HANDLING =====================

window.addEventListener("error", (e) => console.error("Global error:", e.error));
window.addEventListener("unhandledrejection", (e) => console.error("Unhandled rejection:", e.reason));

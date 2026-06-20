// ===================== COMMON.JS - FIXED VERSION =====================

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

    // Initialize header scripts after inject
    setTimeout(() => {
      initScrollEffect();
      initMobileMenu();
      initAccordionDropdowns();
      setActiveMenuItem();
      initKeyboardNavigation();
    }, 100);

    // --- Load Footer ---
    const footerContainer = document.createElement("div");
    footerContainer.id = "footer-container";
    document.body.append(footerContainer);

    const footerRes = await fetch("footer.html");
    if (!footerRes.ok) throw new Error("Footer file not found");
    const footerHTML = await footerRes.text();
    footerContainer.innerHTML = footerHTML;

    // Page-specific scripts
    setTimeout(() => {
      initReviewSlider();
      initFAQAccordion();
    }, 200);

  } catch (e) {
    console.error("❌ Header/Footer load failed:", e);
  }
});

// ===================== SCROLL EFFECT =====================

function initScrollEffect() {
  const header = document.querySelector(".header, header");
  if (!header) return;

  function checkScroll() {
    header.classList.toggle("scrolled", window.scrollY > 10);
  }

  window.addEventListener("scroll", checkScroll, { passive: true });
  checkScroll();
}

// ===================== MOBILE MENU =====================

function initMobileMenu() {
  // Support both id-based and class-based selectors
  const hamburger = document.getElementById("hamburger") || document.querySelector(".hamburger");
  const navMenu   = document.getElementById("navMenu")   || document.querySelector(".nav-menu");

  if (!hamburger || !navMenu) {
    console.warn("⚠️ Mobile menu elements not found");
    return;
  }

  // Support both overlay ids/classes used across pages
  let overlay =
    document.getElementById("mobileOverlay") ||
    document.getElementById("navOverlay")    ||
    document.querySelector(".mobile-overlay") ||
    document.querySelector(".nav-overlay");

  // Create overlay if none found
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.className = "mobile-overlay";
    document.body.appendChild(overlay);
  }

  // ---- Helper functions ----
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

  // Remove stale listeners by cloning hamburger
  const freshHamburger = hamburger.cloneNode(true);
  hamburger.parentNode.replaceChild(freshHamburger, hamburger);

  freshHamburger.addEventListener("click", (e) => {
    e.stopPropagation();
    navMenu.classList.contains("active") ? closeMenu() : openMenu();
  });

  freshHamburger.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      navMenu.classList.contains("active") ? closeMenu() : openMenu();
    }
  });

  overlay.addEventListener("click", closeMenu);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && navMenu.classList.contains("active")) closeMenu();
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 992) closeMenu();
  });

  console.log("✅ Mobile menu initialized");
}

// ===================== ACCORDION DROPDOWNS (MOBILE) =====================

function initAccordionDropdowns() {
  // Use event delegation on document so it works after dynamic header inject
  document.addEventListener("click", handleDropdownClick);
  document.addEventListener("click", handleNavLinkClick);

  console.log("✅ Accordion dropdowns initialized");
}

function handleDropdownClick(e) {
  if (window.innerWidth > 992) return;

  const toggleLink = e.target.closest(".has-dropdown > a, .course-dropdown > a");
  if (!toggleLink) return;

  e.preventDefault();
  e.stopPropagation();

  const parent = toggleLink.closest(".has-dropdown, .course-dropdown");
  if (!parent) return;

  const isActive = parent.classList.contains("active");

  // Close all other dropdowns
  document.querySelectorAll(".has-dropdown.active, .course-dropdown.active").forEach(el => {
    if (el !== parent) el.classList.remove("active");
  });

  parent.classList.toggle("active", !isActive);
}

function handleNavLinkClick(e) {
  if (window.innerWidth > 992) return;

  const link = e.target.closest(".nav-menu a");
  if (!link) return;

  // Skip dropdown parent toggles
  const isDropdownParent =
    (link.closest(".has-dropdown") && link === link.closest(".has-dropdown > a")) ||
    (link.closest(".course-dropdown") && link === link.closest(".course-dropdown > a"));

  if (isDropdownParent) return;

  // Real destination link - close menu
  setTimeout(() => {
    if (window.closeMobileMenu) window.closeMobileMenu();
  }, 150);
}

// ===================== ACTIVE PAGE HIGHLIGHT =====================

function setActiveMenuItem() {
  const currentPage = window.location.pathname.split("/").pop() || "index.html";

  document.querySelectorAll(".nav-menu a").forEach((item) => {
    const href = item.getAttribute("href");
    if (!href) return;
    const hrefPage = href.split("/").pop();
    const isActive = hrefPage === currentPage || (currentPage === "" && hrefPage === "index.html");
    item.classList.toggle("active", isActive);
  });
}

// ===================== KEYBOARD NAVIGATION =====================

function initKeyboardNavigation() {
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && window.closeMobileMenu) {
      window.closeMobileMenu();
    }
  });
}

// ===================== REVIEW SLIDER =====================

function initReviewSlider() {
  const wrapper = document.getElementById("reviewWrapper");
  const dots = document.querySelectorAll(".dot");
  if (!wrapper || dots.length === 0) return;

  const totalSlides = dots.length;
  let currentSlide = 0;
  let slideInterval;

  function moveToSlide(index) {
    currentSlide = index;
    wrapper.style.transform = `translateX(-${index * 100}%)`;
    wrapper.style.transition = "transform 0.5s ease-in-out";
    dots.forEach((dot) => dot.classList.remove("active"));
    if (dots[index]) dots[index].classList.add("active");
  }

  function startAutoSlide() {
    slideInterval = setInterval(() => {
      currentSlide = (currentSlide + 1) % totalSlides;
      moveToSlide(currentSlide);
    }, 5000);
  }

  function stopAutoSlide() {
    clearInterval(slideInterval);
  }

  moveToSlide(0);
  startAutoSlide();

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      stopAutoSlide();
      moveToSlide(index);
      startAutoSlide();
    });
  });

  wrapper.addEventListener("mouseenter", stopAutoSlide);
  wrapper.addEventListener("mouseleave", startAutoSlide);

  document.addEventListener("visibilitychange", () => {
    document.hidden ? stopAutoSlide() : startAutoSlide();
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

      faqItems.forEach((otherItem) => {
        otherItem.classList.remove("active");
        const otherAnswer = otherItem.querySelector(".faq-answer, .faq-content");
        if (otherAnswer) otherAnswer.style.display = "none";
      });

      if (!isActive) {
        item.classList.add("active");
        answer.style.display = "block";
      }
    });
  });
}

// ===================== ERROR HANDLING =====================

window.addEventListener("error", (e) => console.error("Global error:", e.error));
window.addEventListener("unhandledrejection", (e) => console.error("Unhandled rejection:", e.reason));

// ===================== EXPORT =====================
window.initScrollEffect       = initScrollEffect;
window.initMobileMenu         = initMobileMenu;
window.initAccordionDropdowns = initAccordionDropdowns;
window.setActiveMenuItem      = setActiveMenuItem;
window.initReviewSlider       = initReviewSlider;
window.initFAQAccordion       = initFAQAccordion;

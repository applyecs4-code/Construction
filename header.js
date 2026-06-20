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

    // Initialize header scripts after inject - wait for DOM to update
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

    // Page-specific scripts (may not exist on all pages - safe to init)
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
  const header = document.querySelector("header, .header");
  if (!header) {
    console.warn("Header element not found for scroll effect");
    return;
  }

  function checkScroll() {
    if (window.scrollY > 10) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  }

  // Remove existing listeners to avoid duplicates
  window.removeEventListener("scroll", checkScroll);
  window.addEventListener("scroll", checkScroll, { passive: true });
  checkScroll(); // run on load
}

// ===================== MOBILE MENU =====================

function initMobileMenu() {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");

  if (!hamburger || !navMenu) {
    console.warn("⚠️ Mobile menu elements not found");
    return;
  }

  // Create overlay if missing
  let overlay = document.querySelector(".mobile-overlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.className = "mobile-overlay";
    document.body.appendChild(overlay);
  }

  // Remove old event listeners by cloning
  const newHamburger = hamburger.cloneNode(true);
  hamburger.parentNode.replaceChild(newHamburger, hamburger);

  function openMenu() {
    newHamburger.classList.add("active");
    navMenu.classList.add("active");
    overlay.classList.add("active");
    newHamburger.setAttribute("aria-expanded", "true");
    document.body.style.overflow = "hidden";
  }

  function closeMenu() {
    newHamburger.classList.remove("active");
    navMenu.classList.remove("active");
    overlay.classList.remove("active");
    newHamburger.setAttribute("aria-expanded", "false");
    document.body.style.overflow = "";
    // Close all open dropdowns
    document.querySelectorAll(".has-dropdown.active, .course-dropdown.active").forEach(el => {
      el.classList.remove("active");
    });
  }

  // Expose close function globally
  window.closeMobileMenu = closeMenu;

  newHamburger.addEventListener("click", (e) => {
    e.stopPropagation();
    if (navMenu.classList.contains("active")) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  // Keyboard support for hamburger
  newHamburger.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      if (navMenu.classList.contains("active")) {
        closeMenu();
      } else {
        openMenu();
      }
    }
  });

  overlay.addEventListener("click", closeMenu);

  // ESC key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && navMenu.classList.contains("active")) {
      closeMenu();
    }
  });

  // Close menu on resize to desktop
  window.addEventListener("resize", () => {
    if (window.innerWidth > 992 && navMenu.classList.contains("active")) {
      closeMenu();
    }
  });

  console.log("✅ Mobile menu initialized");
}

// ===================== ACCORDION DROPDOWNS (MOBILE) =====================

function initAccordionDropdowns() {
  // Remove existing listeners by using a named handler
  const dropdownHandler = (e) => {
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

    // Close all dropdowns first
    document.querySelectorAll(".has-dropdown.active, .course-dropdown.active").forEach(el => {
      if (el !== parent) {
        el.classList.remove("active");
      }
    });

    // Toggle current
    if (isActive) {
      parent.classList.remove("active");
    } else {
      parent.classList.add("active");
    }
  };

  // Remove old listener and add new
  document.removeEventListener("click", dropdownHandler);
  document.addEventListener("click", dropdownHandler);

  // Handle regular navigation links (non-dropdown) - close mobile menu
  const linkHandler = (e) => {
    if (window.innerWidth > 992) return;

    const link = e.target.closest(".nav-menu a");
    if (!link) return;

    // Skip if it's a dropdown parent toggle
    if (link.closest(".has-dropdown") && link === link.closest(".has-dropdown").querySelector(":scope > a")) return;
    if (link.closest(".course-dropdown") && link === link.closest(".course-dropdown").querySelector(":scope > a")) return;

    // It's a real destination link - close menu after small delay
    setTimeout(() => {
      if (window.closeMobileMenu) window.closeMobileMenu();
    }, 150);
  };

  document.removeEventListener("click", linkHandler);
  document.addEventListener("click", linkHandler);

  console.log("✅ Accordion dropdowns initialized");
}

// ===================== ACTIVE PAGE HIGHLIGHT =====================

function setActiveMenuItem() {
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  
  document.querySelectorAll(".nav-menu a").forEach((item) => {
    const href = item.getAttribute("href");
    if (!href) return;
    
    // Extract page name from href
    const hrefPage = href.split("/").pop();
    
    if (hrefPage === currentPage || (currentPage === "" && hrefPage === "index.html")) {
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
    
    const isDropdownClick = e.target.closest(".has-dropdown") || e.target.closest(".course-dropdown");
    if (!isDropdownClick) {
      document.querySelectorAll(".has-dropdown:hover, .course-dropdown:hover").forEach(el => {
        // Desktop hover handles this automatically
      });
    }
  });

  // Add keyboard navigation for dropdown menus
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      // Close any open mobile menu
      if (window.closeMobileMenu) window.closeMobileMenu();
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
    if (slideInterval) {
      clearInterval(slideInterval);
    }
  }

  // Initialize
  moveToSlide(0);
  startAutoSlide();

  // Dot navigation
  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      stopAutoSlide();
      moveToSlide(index);
      startAutoSlide();
    });
  });

  // Pause on hover
  wrapper.addEventListener("mouseenter", stopAutoSlide);
  wrapper.addEventListener("mouseleave", startAutoSlide);

  // Handle visibility change
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      stopAutoSlide();
    } else {
      startAutoSlide();
    }
  });
}

// ===================== FAQ ACCORDION =====================

function initFAQAccordion() {
  const faqItems = document.querySelectorAll(".faq-item");
  if (faqItems.length === 0) return;

  faqItems.forEach((item) => {
    // Reset state
    item.classList.remove("active");
    const answer = item.querySelector(".faq-answer, .faq-content");
    if (answer) {
      answer.style.display = "none";
    }
  });

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");
    const answer = item.querySelector(".faq-answer, .faq-content");
    if (!question || !answer) return;

    question.addEventListener("click", () => {
      const isActive = item.classList.contains("active");
      
      // Close all other FAQs
      faqItems.forEach((otherItem) => {
        otherItem.classList.remove("active");
        const otherAnswer = otherItem.querySelector(".faq-answer, .faq-content");
        if (otherAnswer) {
          otherAnswer.style.display = "none";
        }
      });

      // Toggle current
      if (!isActive) {
        item.classList.add("active");
        answer.style.display = "block";
      }
    });
  });
}

// ===================== ERROR HANDLING =====================

window.addEventListener("error", (e) => {
  console.error("Global error:", e.error);
});

window.addEventListener("unhandledrejection", (e) => {
  console.error("Unhandled rejection:", e.reason);
});

// ===================== EXPORT FOR EXTERNAL USE =====================
// Make functions available globally if needed
window.initScrollEffect = initScrollEffect;
window.initMobileMenu = initMobileMenu;
window.initAccordionDropdowns = initAccordionDropdowns;
window.setActiveMenuItem = setActiveMenuItem;
window.initReviewSlider = initReviewSlider;
window.initFAQAccordion = initFAQAccordion;

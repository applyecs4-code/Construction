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

    // Initialize header scripts immediately
    initHeaderScripts();

    // --- Load Footer (after header loaded) ---
    const footerContainer = document.createElement("div");
    footerContainer.id = "footer-container";
    document.body.append(footerContainer);

    const footerRes = await fetch("footer.html");
    if (!footerRes.ok) throw new Error("Footer file not found");
    const footerHTML = await footerRes.text();
    footerContainer.innerHTML = footerHTML;

    // Initialize all other scripts with small delay to ensure DOM is ready
    setTimeout(() => {
      initScrollEffect();
      initMobileMenu();
      initAccordionDropdowns();
      initReviewSlider();
      initFAQAccordion();
      setActiveMenuItem();
      initKeyboardNavigation();
    }, 100);

  } catch (e) {
    console.error("❌ Header/Footer load failed:", e);
  }
});

// ===================== HEADER FUNCTIONALITY =====================

function initHeaderScripts() {
  // ✅ Dropdown toggle - केवल desktop के लिए
  window.toggleSubMenu = function (element) {
    if (window.innerWidth > 992) {
      const menu = element.parentElement;
      const allMenus = document.querySelectorAll(".menu");
      allMenus.forEach((m) => {
        if (m !== menu) m.classList.remove("active");
      });
      menu.classList.toggle("active");
    }
  };

  // ✅ Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".menu")) {
      document
        .querySelectorAll(".menu")
        .forEach((menu) => menu.classList.remove("active"));
    }
  });
}

// ===================== MOBILE MENU (SINGLE FUNCTION) =====================

function initMobileMenu() {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");
  const mobileOverlay = document.querySelector(".mobile-overlay");

  if (!hamburger || !navMenu) {
    console.log("Mobile menu elements not found");
    return;
  }

  // Create overlay if it doesn't exist
  if (!mobileOverlay) {
    const overlay = document.createElement("div");
    overlay.className = "mobile-overlay";
    document.body.appendChild(overlay);
  }

  const overlay = document.querySelector(".mobile-overlay");

  // ✅ Global function to open/close mobile menu
  window.toggleMobileMenu = function (forceClose = false) {
    const isActive = navMenu.classList.contains("active");

    if (forceClose) {
      // Force close
      hamburger.classList.remove("active");
      navMenu.classList.remove("active");
      overlay.classList.remove("active");
      hamburger.setAttribute("aria-expanded", "false");
      document.body.style.overflow = "";
    } else {
      // Toggle
      hamburger.classList.toggle("active");
      navMenu.classList.toggle("active");
      overlay.classList.toggle("active");
      const isExpanded = navMenu.classList.contains("active");
      hamburger.setAttribute("aria-expanded", isExpanded);
      document.body.style.overflow = isExpanded ? "hidden" : "";
    }
  };

  // ✅ Hamburger click
  hamburger.addEventListener("click", (e) => {
    e.stopPropagation();
    window.toggleMobileMenu();
  });

  // ✅ Overlay click
  overlay.addEventListener("click", () => {
    window.toggleMobileMenu(true);
  });

  // ✅ Close menu when clicking on nav links
  document.querySelectorAll(".nav-menu a").forEach((link) => {
    link.addEventListener("click", () => {
      window.toggleMobileMenu(true);
    });
  });

  console.log("Mobile menu initialized");
}

// ===================== SCROLL EFFECT =====================

function initScrollEffect() {
  const header = document.querySelector("header");
  if (!header) return;

  window.addEventListener("scroll", function () {
    if (window.scrollY > 10) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });

  // Initialize scroll state
  if (window.scrollY > 10) {
    header.classList.add("scrolled");
  }
}

// ===================== ACCORDION DROPDOWNS =====================

function initAccordionDropdowns() {
  // Mobile dropdown toggle
  const dropdowns = document.querySelectorAll(".dropdown");

  dropdowns.forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    if (toggle) {
      toggle.addEventListener("click", (e) => {
        if (window.innerWidth <= 992) {
          e.preventDefault();
          e.stopPropagation();

          // Close all other dropdowns
          dropdowns.forEach((d) => {
            if (d !== dropdown) {
              d.classList.remove("active");
              const otherToggle = d.querySelector(".dropdown-toggle");
              if (otherToggle) {
                otherToggle.setAttribute("aria-expanded", "false");
              }
            }
          });

          // Toggle current dropdown
          dropdown.classList.toggle("active");
          const isExpanded = dropdown.classList.contains("active");
          toggle.setAttribute("aria-expanded", isExpanded);
        }
      });
    }
  });

  // Accordion functionality
  const accordionHeaders = document.querySelectorAll(".accordion-header");
  accordionHeaders.forEach((button) => {
    button.addEventListener("click", () => {
      const content = button.nextElementSibling;
      const isMobile = window.innerWidth <= 992;

      if (isMobile) {
        // Mobile behavior - toggle accordion
        const isActive = button.classList.contains("active");

        // Close all other accordions
        document.querySelectorAll(".accordion-header").forEach((h) => {
          if (h !== button) {
            h.classList.remove("active");
            const otherContent = h.nextElementSibling;
            if (otherContent) {
              otherContent.style.display = "none";
              otherContent.classList.remove("active");
            }
          }
        });

        // Toggle current accordion
        button.classList.toggle("active");
        if (content) {
          content.classList.toggle("active");
          content.style.display = isActive ? "none" : "block";
        }
      }
    });
  });

  // Initialize mobile accordion state
  if (window.innerWidth <= 992) {
    document.querySelectorAll(".accordion-content").forEach((content) => {
      if (!content.classList.contains("active")) {
        content.style.display = "none";
      }
    });
  }

  // Reset on window resize
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      const isDesktop = window.innerWidth > 992;

      if (isDesktop) {
        // Reset dropdowns and accordions for desktop
        dropdowns.forEach((dropdown) => {
          dropdown.classList.remove("active");
          const toggle = dropdown.querySelector(".dropdown-toggle");
          if (toggle) {
            toggle.setAttribute("aria-expanded", "false");
          }
        });

        document.querySelectorAll(".accordion-content").forEach((content) => {
          content.style.display = "";
          content.classList.remove("active");
        });

        document.querySelectorAll(".accordion-header").forEach((button) => {
          button.classList.remove("active");
        });
      } else {
        // On mobile, ensure accordion contents are hidden by default
        document.querySelectorAll(".accordion-content").forEach((content) => {
          if (!content.classList.contains("active")) {
            content.style.display = "none";
          }
        });
      }
    }, 100);
  });
}

// ===================== HELPER FUNCTIONS =====================

function closeAllMenus() {
  // Close mobile menu
  const navMenu = document.querySelector(".nav-menu");
  const hamburger = document.querySelector(".hamburger");
  const mobileOverlay = document.querySelector(".mobile-overlay");

  if (navMenu) navMenu.classList.remove("active");
  if (hamburger) hamburger.classList.remove("active");
  if (mobileOverlay) mobileOverlay.classList.remove("active");

  // Close dropdowns
  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    dropdown.classList.remove("active");
    const toggle = dropdown.querySelector(".dropdown-toggle");
    if (toggle) {
      toggle.setAttribute("aria-expanded", "false");
    }
  });

  // Close desktop menus
  document
    .querySelectorAll(".menu")
    .forEach((menu) => menu.classList.remove("active"));

  document.body.style.overflow = "";
}

// ===================== REVIEW SLIDER =====================

function initReviewSlider() {
  const wrapper = document.getElementById("reviewWrapper");
  const dots = document.querySelectorAll(".dot");
  const totalSlides = dots.length;

  if (!wrapper || totalSlides === 0) {
    console.log("Review slider elements not found");
    return;
  }

  let currentSlide = 0;

  function moveToSlide(slideIndex) {
    currentSlide = slideIndex;
    wrapper.style.transform = `translateX(-${slideIndex * 100}%)`;

    dots.forEach((dot) => dot.classList.remove("active"));
    if (dots[slideIndex]) {
      dots[slideIndex].classList.add("active");
    }
  }

  // Auto slide every 5 sec
  const slideInterval = setInterval(() => {
    if (totalSlides > 0) {
      currentSlide = (currentSlide + 1) % totalSlides;
      moveToSlide(currentSlide);
    }
  }, 5000);

  // Initialize dots click events
  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => moveToSlide(index));
  });

  // Clear interval when page is hidden
  document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
      clearInterval(slideInterval);
    }
  });
}

// ===================== FAQ ACCORDION =====================

function initFAQAccordion() {
  const faqItems = document.querySelectorAll(".faq-item");

  if (faqItems.length === 0) {
    console.log("FAQ items not found");
    return;
  }

  // 🔹 Start: sab FAQ band rakho
  faqItems.forEach((item) => {
    item.classList.remove("active");
    const answer = item.querySelector(".faq-answer, .faq-content");
    if (answer) {
      answer.style.display = "none";
    }
  });

  // 🔹 Click par open/close (multiple open allow)
  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");
    const answer = item.querySelector(".faq-answer, .faq-content");
    if (!question || !answer) return;

    question.addEventListener("click", () => {
      const isActive = item.classList.contains("active");

      if (isActive) {
        // Ab band karo
        item.classList.remove("active");
        answer.style.display = "none";
      } else {
        // Ab open karo
        item.classList.add("active");
        answer.style.display = "block";
      }
    });
  });
}

// ===================== ACTIVE PAGE HIGHLIGHT =====================

function setActiveMenuItem() {
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  const menuItems = document.querySelectorAll(".nav-menu a");

  menuItems.forEach((item) => {
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
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeAllMenus();
    }
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    const dropdowns = document.querySelectorAll(".dropdown");
    if (!e.target.closest(".dropdown") && !e.target.closest(".hamburger")) {
      dropdowns.forEach((dropdown) => {
        dropdown.classList.remove("active");
        const toggle = dropdown.querySelector(".dropdown-toggle");
        if (toggle) {
          toggle.setAttribute("aria-expanded", "false");
        }
      });
    }
  });
}

// ===================== ERROR HANDLING =====================

// Global error handler
window.addEventListener("error", function (e) {
  console.error("Global error:", e.error);
});

// Promise rejection handler
window.addEventListener("unhandledrejection", function (e) {
  console.error("Unhandled promise rejection:", e.reason);
});
    
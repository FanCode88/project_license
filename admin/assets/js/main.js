/**
 * Template Name: Delicious - v4.1.0 - MULTIPAGE FIX
 */
(function () {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    if (!el || typeof el !== "string") return null;
    el = el.trim();

    // Dacă începe cu #, verificăm dacă este un selector valid pentru CSS
    if (el.startsWith("#") && el.includes("/")) return null;

    try {
      if (all) {
        return [...document.querySelectorAll(el)];
      } else {
        return document.querySelector(el);
      }
    } catch (e) {
      // Dacă selectorul e invalid (ex: "admin/specials.php"), returnăm null în loc să crăpăm situl
      return null;
    }
  };

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all);
    if (selectEl) {
      if (all) {
        selectEl.forEach((e) => e.addEventListener(type, listener));
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };

  /**
   * Easy on scroll event listener
   */
  const onscroll = (el, listener) => {
    if (el) el.addEventListener("scroll", listener);
  };

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select("#navbar .scrollto", true);
  const navbarlinksActive = () => {
    let position = window.scrollY + 200;
    if (!navbarlinks) return;
    navbarlinks.forEach((navbarlink) => {
      if (!navbarlink.hash) return;

      // Verifică dacă hash-ul este valid pentru querySelector
      let hash = navbarlink.hash;
      if (hash.startsWith("#")) {
        let section = select(hash);
        if (!section) return;
        if (position >= section.offsetTop && position <= section.offsetTop + section.offsetHeight) {
          navbarlink.classList.add("active");
        } else {
          navbarlink.classList.remove("active");
        }
      }
    });
  };
  window.addEventListener("load", navbarlinksActive);
  onscroll(document, navbarlinksActive);

  /**
   * Scrolls to an element with header offset
   */
  const scrollto = (el) => {
    let header = select("#header");
    if (!header) return;
    let offset = header.offsetHeight;

    let targetEl = select(el);
    if (!targetEl) return;

    let elementPos = targetEl.offsetTop;
    window.scrollTo({
      top: elementPos - offset,
      behavior: "smooth",
    });
  };

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select("#header");
  let selectTopbar = select("#topbar");
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add("header-scrolled");
        if (selectTopbar) {
          selectTopbar.classList.add("topbar-scrolled");
        }
      } else {
        selectHeader.classList.remove("header-scrolled");
        if (selectTopbar) {
          selectTopbar.classList.remove("topbar-scrolled");
        }
      }
    };
    window.addEventListener("load", headerScrolled);
    onscroll(document, headerScrolled);
  }

  /**
   * Back to top button
   */
  let backtotop = select(".back-to-top");
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add("active");
      } else {
        backtotop.classList.remove("active");
      }
    };
    window.addEventListener("load", toggleBacktotop);
    onscroll(document, toggleBacktotop);
  }

  /**
   * Mobile nav toggle
   */
  on("click", ".mobile-nav-toggle", function (e) {
    let navbar = select("#navbar");
    if (navbar) {
      navbar.classList.toggle("navbar-mobile");
      this.classList.toggle("bi-list");
      this.classList.toggle("bi-x");
    }
  });

  /**
   * Mobile nav dropdowns activate
   */
  on(
    "click",
    ".navbar .dropdown > a",
    function (e) {
      let navbar = select("#navbar");
      if (navbar && navbar.classList.contains("navbar-mobile")) {
        e.preventDefault();
        if (this.nextElementSibling) {
          this.nextElementSibling.classList.toggle("dropdown-active");
        }
      }
    },
    true,
  );

  /**
   * Scroll cu offset pe link-urile cu clasa .scrollto
   */
  on(
    "click",
    ".scrollto",
    function (e) {
      // Verificăm dacă hash-ul este valid și există în pagină
      if (this.hash && select(this.hash)) {
        e.preventDefault();

        let navbar = select("#navbar");
        if (navbar.classList.contains("navbar-mobile")) {
          navbar.classList.remove("navbar-mobile");
          let navbarToggle = select(".mobile-nav-toggle");
          navbarToggle.classList.toggle("bi-list");
          navbarToggle.classList.toggle("bi-x");
        }
        scrollto(this.hash);
      }
      // DACA NU EXISTĂ (ex: ești pe altă pagină sau calea e externă),
      // JS nu mai face nimic, lasă browserul să schimbe pagina normal!
    },
    true,
  );

  /**
   * Scroll cu offset la încărcarea paginii dacă URL-ul are hash
   */
  window.addEventListener("load", () => {
    if (window.location.hash) {
      let hash = window.location.hash;
      if (select(hash)) {
        scrollto(hash);
      }
    }
  });

  /**
   * Hero carousel indicators
   */
  let heroCarouselIndicators = select("#hero-carousel-indicators");
  let heroCarouselItems = select("#heroCarousel .carousel-item", true);

  if (heroCarouselIndicators && heroCarouselItems.length > 0) {
    heroCarouselItems.forEach((item, index) => {
      index === 0
        ? (heroCarouselIndicators.innerHTML += "<li data-bs-target='#heroCarousel' data-bs-slide-to='" + index + "' class='active'></li>")
        : (heroCarouselIndicators.innerHTML += "<li data-bs-target='#heroCarousel' data-bs-slide-to='" + index + "'></li>");
    });
  }

  /**
   * Menu isotope and filter
   */
  window.addEventListener("load", () => {
    let menuContainer = select(".menu-container");
    // Adăugat verificare obligatorie! Execută doar dacă Isotope și containerul există
    if (menuContainer && typeof Isotope !== "undefined") {
      let menuIsotope = new Isotope(menuContainer, {
        itemSelector: ".menu-item",
        layoutMode: "fitRows",
      });

      let menuFilters = select("#menu-flters li", true);

      on(
        "click",
        "#menu-flters li",
        function (e) {
          e.preventDefault();
          if (menuFilters) {
            menuFilters.forEach(function (el) {
              el.classList.remove("filter-active");
            });
          }
          this.classList.add("filter-active");

          menuIsotope.arrange({
            filter: this.getAttribute("data-filter"),
          });
        },
        true,
      );
    }
  });

  /**
   * Events slider
   */
  if (select(".events-slider") && typeof Swiper !== "undefined") {
    new Swiper(".events-slider", {
      speed: 600,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      slidesPerView: "auto",
      pagination: {
        el: ".swiper-pagination",
        type: "bullets",
        clickable: true,
      },
    });
  }

  /**
   * Initiate gallery lightbox
   */
  if (select(".gallery-lightbox") && typeof GLightbox !== "undefined") {
    const galleryLightbox = GLightbox({
      selector: ".gallery-lightbox",
    });
  }

  /**
   * Testimonials slider
   */
  if (select(".testimonials-slider") && typeof Swiper !== "undefined") {
    new Swiper(".testimonials-slider", {
      speed: 600,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      slidesPerView: "auto",
      pagination: {
        el: ".swiper-pagination",
        type: "bullets",
        clickable: true,
      },
    });
  }
})();

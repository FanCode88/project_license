(function () {
  "use strict";

  // 1. SELECTOR REPARAT: Returnează null dacă elementul nu există, fără să crape
  const select = (el, all = false) => {
    if (!el) return null;
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

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

  // Restul funcțiilor de utilitate rămân la fel
  const onscroll = (el, listener) => {
    el.addEventListener("scroll", listener);
  };

  // 2. NAVBAR: Verificăm existența înainte de forEach
  let navbarlinks = select("#navbar .scrollto", true);
  const navbarlinksActive = () => {
    if (!navbarlinks) return;
    let position = window.scrollY + 200;
    navbarlinks.forEach((navbarlink) => {
      if (!navbarlink.hash) return;
      let section = select(navbarlink.hash);
      if (!section) return;
      if (position >= section.offsetTop && position <= section.offsetTop + section.offsetHeight) {
        navbarlink.classList.add("active");
      } else {
        navbarlink.classList.remove("active");
      }
    });
  };
  window.addEventListener("load", navbarlinksActive);
  onscroll(document, navbarlinksActive);

  const scrollto = (el) => {
    let header = select("#header");
    if (!header) return;
    let offset = header.offsetHeight;
    let element = select(el);
    if (element) {
      window.scrollTo({ top: element.offsetTop - offset, behavior: "smooth" });
    }
  };

  // 3. HEADER SCROLLED: Protejat de IF
  let selectHeader = select("#header");
  let selectTopbar = select("#topbar");
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add("header-scrolled");
        if (selectTopbar) selectTopbar.classList.add("topbar-scrolled");
      } else {
        selectHeader.classList.remove("header-scrolled");
        if (selectTopbar) selectTopbar.classList.remove("topbar-scrolled");
      }
    };
    window.addEventListener("load", headerScrolled);
    onscroll(document, headerScrolled);
  }

  // 4. BACK TO TOP: Protejat
  let backtotop = select(".back-to-top");
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) backtotop.classList.add("active");
      else backtotop.classList.remove("active");
    };
    window.addEventListener("load", toggleBacktotop);
    onscroll(document, toggleBacktotop);
  }

  // 5. CAROUSEL: Verificare obligatorie
  let heroCarouselIndicators = select("#hero-carousel-indicators");
  let heroCarouselItems = select("#heroCarousel .carousel-item", true);
  if (heroCarouselIndicators && heroCarouselItems) {
    heroCarouselItems.forEach((item, index) => {
      heroCarouselIndicators.innerHTML +=
        index === 0
          ? "<li data-bs-target='#heroCarousel' data-bs-slide-to='" + index + "' class='active'></li>"
          : "<li data-bs-target='#heroCarousel' data-bs-slide-to='" + index + "'></li>";
    });
  }

  // 6. ISOTOPE (Menu): Verificare obligatorie
  window.addEventListener("load", () => {
    let menuContainer = select(".menu-container");
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
          menuFilters.forEach((el) => el.classList.remove("filter-active"));
          this.classList.add("filter-active");
          menuIsotope.arrange({ filter: this.getAttribute("data-filter") });
        },
        true,
      );
    }
  });

  // 7. SWIPER: Verificare clase existente
  if (select(".events-slider")) {
    new Swiper(".events-slider", {
      /* ...config... */
    });
  }

  if (select(".testimonials-slider")) {
    new Swiper(".testimonials-slider", {
      /* ...config... */
    });
  }

  // 8. LIGHTBOX
  if (select(".gallery-lightbox")) {
    GLightbox({ selector: ".gallery-lightbox" });
  }
})();

// Adaugă asta în main.js, după ce se închide funcția principală
window.cancelReservation = function (resId, btnElement) {
  if (!confirm("Ești sigur că vrei să anulezi această rezervare?")) return;

  fetch("delete-reservation.php?id=" + resId)
    .then((response) => response.text())
    .then((data) => {
      if (data.trim() === "success") {
        btnElement.closest("tr").remove();
      } else {
        alert("Eroare la ștergerea rezervării.");
      }
    })
    .catch((error) => console.error("Eroare:", error));
};

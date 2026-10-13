document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".prcw-event-year-gallery").forEach((block) => {
    const dataEl = block.querySelector(".prcw-event-year-gallery__data");
    if (!dataEl) return;

    let photos = [];
    try {
      photos = JSON.parse(dataEl.textContent);
    } catch (e) {
      return;
    }

    const lightbox = block.querySelector(".prcw-event-year-gallery__lightbox");
    const lightboxImg = block.querySelector(
      ".prcw-event-year-gallery__lightbox-img",
    );
    const closeBtn = block.querySelector(
      ".prcw-event-year-gallery__lightbox-close",
    );
    const prevBtn = block.querySelector(
      ".prcw-event-year-gallery__lightbox-prev",
    );
    const nextBtn = block.querySelector(
      ".prcw-event-year-gallery__lightbox-next",
    );
    const buttons = block.querySelectorAll(".prcw-event-year-gallery__photo");

    let currentIndex = 0;

    function openLightbox(index) {
      currentIndex = index;
      lightboxImg.src = photos[currentIndex];
      lightbox.classList.add("is-active");
    }

    function closeLightbox() {
      lightbox.classList.remove("is-active");
    }

    function showPrev() {
      currentIndex = (currentIndex - 1 + photos.length) % photos.length;
      lightboxImg.src = photos[currentIndex];
    }

    function showNext() {
      currentIndex = (currentIndex + 1) % photos.length;
      lightboxImg.src = photos[currentIndex];
    }

    buttons.forEach((btn) => {
      btn.addEventListener("click", () => {
        openLightbox(parseInt(btn.getAttribute("data-lightbox-index"), 10));
      });
    });

    closeBtn.addEventListener("click", closeLightbox);
    prevBtn.addEventListener("click", showPrev);
    nextBtn.addEventListener("click", showNext);

    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) closeLightbox();
    });

    document.addEventListener("keydown", (e) => {
      if (!lightbox.classList.contains("is-active")) return;
      if (e.key === "Escape") closeLightbox();
      if (e.key === "ArrowLeft") showPrev();
      if (e.key === "ArrowRight") showNext();
    });
  });
});

import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';

const initHeroSliders = () => {
  document.querySelectorAll('.prcw-hero-slider').forEach((el) => {
    const swiperEl = el.querySelector('.swiper');
    const wrapperEl = el.querySelector('.swiper-wrapper');
    if (!swiperEl || !wrapperEl || swiperEl.swiper) {
      return;
    }

    const slides = wrapperEl.querySelectorAll('.swiper-slide');
    const slideCount = slides.length;
    const nextEl = el.querySelector('.prcw-hero-slider__next');
    const prevEl = el.querySelector('.prcw-hero-slider__prev');

    if (slideCount <= 1) {
      if (nextEl) nextEl.style.display = 'none';
      if (prevEl) prevEl.style.display = 'none';
      return;
    }

    // Swiper v11+ requiere >= 3 slides en DOM para loop bidireccional fluido sin saltos al arrastrar
    if (slideCount === 2) {
      slides.forEach((slide) => {
        const clone = slide.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        wrapperEl.appendChild(clone);
      });
    }

    new Swiper(swiperEl, {
      modules: [Navigation, Autoplay],
      slidesPerView: 1,
      loop: true,
      autoplay: {
        delay: 6000,
        disableOnInteraction: false,
      },
      navigation: {
        nextEl,
        prevEl,
      },
    });
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initHeroSliders);
} else {
  initHeroSliders();
}

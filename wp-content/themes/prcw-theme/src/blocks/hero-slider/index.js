import 'swiper/css';
import 'swiper/css/navigation';
import './style.scss';
import './editor.scss';
import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.prcw-hero-slider').forEach((el) => {
    new Swiper(el.querySelector('.swiper'), {
      modules: [Navigation, Autoplay],
      loop: true,
      autoplay: {
        delay: 6000,
        disableOnInteraction: false,
      },
      navigation: {
        nextEl: el.querySelector('.prcw-hero-slider__next'),
        prevEl: el.querySelector('.prcw-hero-slider__prev'),
      },
    });
  });
});

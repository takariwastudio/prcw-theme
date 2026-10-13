document.addEventListener('DOMContentLoaded', () => {
  // Respeta la preferencia de accesibilidad del usuario (menos movimiento)
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const blocks = document.querySelectorAll('[class*="wp-block-prcw-"]');

  if (prefersReducedMotion || !('IntersectionObserver' in window) || blocks.length === 0) {
    blocks.forEach((block) => block.classList.add('prcw-in-view'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('prcw-in-view');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
  );

  blocks.forEach((block) => {
    block.classList.add('prcw-animate');
    observer.observe(block);
  });
});

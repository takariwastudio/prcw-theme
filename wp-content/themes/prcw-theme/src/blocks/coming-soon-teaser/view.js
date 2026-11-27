const initComingSoonTeasers = () => {
  document.querySelectorAll('.prcw-coming-soon a[href^="#"]').forEach((link) => {
    if (link.dataset.prcwScrollBound === '1') {
      return;
    }
    link.dataset.prcwScrollBound = '1';

    link.addEventListener('click', (e) => {
      const hash = link.getAttribute('href');
      if (!hash || hash.length < 2) {
        return;
      }

      const targetId = hash.slice(1);
      const targetEl = document.getElementById(targetId);
      if (!targetEl) {
        return;
      }

      e.preventDefault();
      targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

      const firstInput = targetEl.querySelector(
        'input[type="email"], input[type="text"]',
      );
      if (firstInput) {
        setTimeout(() => {
          firstInput.focus({ preventScroll: true });
        }, 550);
      }
    });
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initComingSoonTeasers);
} else {
  initComingSoonTeasers();
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.prcw-sponsors').forEach((block) => {
    const tabs = block.querySelectorAll('.prcw-sponsors__tab');
    const panels = block.querySelectorAll('.prcw-sponsors__panel');

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.getAttribute('data-tab');

        tabs.forEach((t) => t.classList.remove('is-active'));
        panels.forEach((p) => p.classList.remove('is-active'));

        tab.classList.add('is-active');
        const panel = Array.from(panels).find((p) => p.getAttribute('data-tab') === target);
        if (panel) {
          panel.classList.add('is-active');
        }
      });
    });
  });
});

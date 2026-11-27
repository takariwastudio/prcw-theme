const pad2 = (num) => String(num).padStart(2, '0');

const initCountdowns = () => {
  document.querySelectorAll('.prcw-countdown').forEach((block) => {
    if (block.dataset.prcwCountdownReady === '1') {
      return;
    }
    block.dataset.prcwCountdownReady = '1';

    const targetIso = block.getAttribute('data-target-utc');
    if (!targetIso) {
      return;
    }

    const targetMs = Date.parse(targetIso);
    if (!Number.isFinite(targetMs)) {
      return;
    }

    const daysEl = block.querySelector('[data-unit="days"]');
    const hoursEl = block.querySelector('[data-unit="hours"]');
    const minutesEl = block.querySelector('[data-unit="minutes"]');
    const secondsEl = block.querySelector('[data-unit="seconds"]');
    const timerEl = block.querySelector('.prcw-countdown__timer');
    const liveEl = block.querySelector('.prcw-countdown__live');

    let intervalId = null;

    const tick = () => {
      const diffMs = targetMs - Date.now();

      if (diffMs <= 0) {
        if (daysEl) daysEl.textContent = '00';
        if (hoursEl) hoursEl.textContent = '00';
        if (minutesEl) minutesEl.textContent = '00';
        if (secondsEl) secondsEl.textContent = '00';
        if (timerEl && liveEl) {
          timerEl.classList.add('is-complete');
          liveEl.classList.add('is-visible');
        }
        if (intervalId) {
          clearInterval(intervalId);
        }
        return;
      }

      const totalSeconds = Math.floor(diffMs / 1000);
      const days = Math.floor(totalSeconds / 86400);
      const hours = Math.floor((totalSeconds % 86400) / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;

      if (daysEl) daysEl.textContent = days >= 100 ? String(days) : pad2(days);
      if (hoursEl) hoursEl.textContent = pad2(hours);
      if (minutesEl) minutesEl.textContent = pad2(minutes);
      if (secondsEl) secondsEl.textContent = pad2(seconds);
    };

    const icsBtn = block.querySelector('[data-ics]');
    if (icsBtn && typeof Blob !== 'undefined' && window.URL && URL.createObjectURL) {
      const icsContent = icsBtn.getAttribute('data-ics');
      if (icsContent) {
        const blob = new Blob([icsContent], {
          type: 'text/calendar;charset=utf-8',
        });
        icsBtn.href = URL.createObjectURL(blob);
      }
    }

    tick();
    intervalId = setInterval(tick, 1000);
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCountdowns);
} else {
  initCountdowns();
}

document.addEventListener("DOMContentLoaded", () => {
  const images = document.querySelectorAll(".prcw-full-bleed-image__img");

  if (!("IntersectionObserver" in window) || images.length === 0) {
    images.forEach((img) => img.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15 },
  );

  images.forEach((img) => observer.observe(img));
});

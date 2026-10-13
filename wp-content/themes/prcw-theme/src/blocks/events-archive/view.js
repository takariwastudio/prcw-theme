document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".prcw-events-archive").forEach((block) => {
    const tabs = block.querySelectorAll(".prcw-events-archive__tab");
    const panels = block.querySelectorAll(".prcw-events-archive__panel");

    tabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        const target = tab.getAttribute("data-target");

        tabs.forEach((t) => t.classList.remove("is-active"));
        panels.forEach((p) => p.classList.remove("is-active"));

        tab.classList.add("is-active");
        block.querySelector("#" + target).classList.add("is-active");
      });
    });
  });
});

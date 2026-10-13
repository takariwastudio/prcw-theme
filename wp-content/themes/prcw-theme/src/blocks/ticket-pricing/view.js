document.addEventListener(
  "click",
  (e) => {
    const btn = e.target.closest(
      '.prcw-ticket-pricing form.cart [name="add-to-cart"]',
    );
    if (!btn) {
      return;
    }

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    const form = btn.closest("form.cart");
    const block = btn.closest(".prcw-ticket-pricing");
    const modal = block
      ? block.querySelector(".prcw-ticket-pricing__modal")
      : null;

    if (!form || !modal) {
      return;
    }

    const formData = new FormData(form);
    formData.append("add-to-cart", btn.value);
    formData.append("product_id", btn.value);

    const ajaxUrl =
      window.wc_add_to_cart_params && wc_add_to_cart_params.wc_ajax_url
        ? wc_add_to_cart_params.wc_ajax_url
            .toString()
            .replace("%%endpoint%%", "add_to_cart")
        : "/?wc-ajax=add_to_cart";

    fetch(ajaxUrl, { method: "POST", body: formData })
      .then((res) => res.json())
      .then((data) => {
        if (data && data.error) {
          window.location.href = form.action;
          return;
        }

        modal.classList.add("is-active");

        try {
          if (window.wp && wp.data && wp.data.dispatch("wc/store/cart")) {
            wp.data.dispatch("wc/store/cart").invalidateResolutionForStore();
          }
        } catch (err) {
          // silencioso a propósito
        }
      })
      .catch(() => {
        window.location.href = form.action;
      });
  },
  true,
);

document.addEventListener("click", (e) => {
  const closeBtn = e.target.closest(".prcw-ticket-pricing__modal-close");
  const overlay = e.target.closest(".prcw-ticket-pricing__modal-overlay");

  if (closeBtn || overlay) {
    const modal = e.target.closest(".prcw-ticket-pricing__modal");
    if (modal) {
      modal.classList.remove("is-active");
    }
  }
});

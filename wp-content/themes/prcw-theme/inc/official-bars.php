<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Obtiene la API key de LocationIQ sin exponerla en el repo.
 * Prioridad: constante PRCW_LOCATIONIQ_API_KEY > LOCATIONIQ_API_KEY > option > ACF option > filtro.
 * Define en wp-config.php: define('PRCW_LOCATIONIQ_API_KEY', 'pk.xxx');
 * O en .wp-env.json: "config": { "PRCW_LOCATIONIQ_API_KEY": "pk.xxx" }
 * O via filtro: add_filter('prcw_locationiq_api_key', fn() => 'pk.xxx');
 */
function prcw_get_locationiq_api_key() {
    if ( defined( 'PRCW_LOCATIONIQ_API_KEY' ) && PRCW_LOCATIONIQ_API_KEY ) {
        return PRCW_LOCATIONIQ_API_KEY;
    }
    if ( defined( 'LOCATIONIQ_API_KEY' ) && LOCATIONIQ_API_KEY ) {
        return LOCATIONIQ_API_KEY;
    }
    $opt = get_option( 'prcw_locationiq_api_key' );
    if ( $opt ) {
        return $opt;
    }
    if ( function_exists( 'get_field' ) ) {
        $acf = get_field( 'locationiq_api_key', 'option' );
        if ( $acf ) {
            return $acf;
        }
    }
    return apply_filters( 'prcw_locationiq_api_key', '' );
}

/**
 * Fuerza el redirect a checkout (en vez de al carrito)
 * SOLO cuando se agrega uno de los 2 productos de Official Bars.
 */
function prcw_bar_subscription_redirect_to_checkout( $url ) {
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_checkout_url' ) ) {
        return $url;
    }

    if ( isset( $_GET['add-to-cart'] ) ) {
        $product_id  = absint( wp_unslash( $_GET['add-to-cart'] ) );
        $new_id      = function_exists( 'prcw_get_bar_new_product_id' ) ? prcw_get_bar_new_product_id() : 143;
        $renewal_id  = function_exists( 'prcw_get_bar_renewal_product_id' ) ? prcw_get_bar_renewal_product_id() : 144;
        $allowed_ids = array_filter( array( $new_id, $renewal_id ) );

        if ( $product_id > 0 && in_array( $product_id, $allowed_ids, true ) ) {
            return wc_get_checkout_url();
        }
    }
    return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'prcw_bar_subscription_redirect_to_checkout' );

/**
 * JS: actualiza el precio en vivo según la selección, y redirige a checkout
 * después de un envío exitoso — SOLO si el formulario tiene el campo
 * "subscription-type" (así no afecta a ningún otro CF7 del sitio).
 */
function prcw_official_bars_script() {
    if ( ! shortcode_exists( 'contact-form-7' ) && ! class_exists( 'WPCF7' ) ) {
        return;
    }

    $locationiq_key     = prcw_get_locationiq_api_key();
    $new_product_id     = function_exists( 'prcw_get_bar_new_product_id' ) ? prcw_get_bar_new_product_id() : 143;
    $renewal_product_id = function_exists( 'prcw_get_bar_renewal_product_id' ) ? prcw_get_bar_renewal_product_id() : 144;
    $wc_active          = class_exists( 'WooCommerce' );
    ?>
    <script>
    document.addEventListener('wpcf7mailsent', function (event) {
        var form = event.target;
        var selected = form.querySelector('input[name="subscription-type"]:checked');

        if (!selected) {
            return; // no es nuestro formulario, no hacemos nada
        }

        var wcActive = <?php echo $wc_active ? 'true' : 'false'; ?>;
        var productId = 0;

        if (selected.value === 'New Subscription') {
            productId = <?php echo intval( $new_product_id ); ?>;
        } else if (selected.value === 'Subscription Renewal') {
            productId = <?php echo intval( $renewal_product_id ); ?>;
        }

        if (wcActive && productId > 0) {
            window.location.href = '<?php echo esc_url( home_url( '/' ) ); ?>?add-to-cart=' + productId;
        }
    }, false);

    document.addEventListener('change', function (e) {
        if (e.target.name === 'subscription-type') {
            var priceEl = document.getElementById('prcw-subscription-price');
            if (!priceEl) return;
            if (e.target.value === 'New Subscription') {
                priceEl.textContent = '$450.00';
            } else if (e.target.value === 'Subscription Renewal') {
                priceEl.textContent = '$400.00';
            }
        }
    });
// Autocomplete de dirección con LocationIQ (requiere API key configurada vía constante/filtro)
    (function () {
        var input = document.querySelector('input[name="bar-address"]');
        if (!input) return;

        var locationIqKey = '<?php echo esc_js( $locationiq_key ); ?>';
        if (!locationIqKey) {
            console.warn('PRCW: LocationIQ API key no configurada. Define PRCW_LOCATIONIQ_API_KEY en wp-config.php o en PRCW Settings.');
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'prcw-address-autocomplete';
        input.parentNode.insertBefore(wrapper, input.nextSibling);
        wrapper.appendChild(input);

        var list = document.createElement('ul');
        list.className = 'prcw-address-suggestions';
        wrapper.appendChild(list);

        var mapContainer = document.getElementById('prcw-address-map');
        var mapIframe = null;
        if (mapContainer) {
            mapIframe = document.createElement('iframe');
            mapIframe.title = 'Bar Location Preview';
            mapIframe.loading = 'lazy';
            mapIframe.src = 'https://www.openstreetmap.org/export/embed.html?bbox=-67.35%2C17.85%2C-65.20%2C18.58&layer=mapnik';
            mapContainer.innerHTML = '';
            mapContainer.appendChild(mapIframe);
        }

        var debounceTimer;

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var query = input.value.trim();

            if (query.length < 3) {
                list.innerHTML = '';
                return;
            }

            list.innerHTML = '<li class="prcw-address-loading">Searching, please wait...</li>';

            debounceTimer = setTimeout(function () {
                var controller = new AbortController();
                var timeoutId = setTimeout(function () { controller.abort(); }, 5000);

                fetch('https://api.locationiq.com/v1/autocomplete?key=' + encodeURIComponent(locationIqKey) + '&q=' + encodeURIComponent(query) + '&countrycodes=pr,us&limit=5', { signal: controller.signal })
                    .then(function (res) {
                        clearTimeout(timeoutId);
                        if (!res.ok) {
                            return [];
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        list.innerHTML = '';
                        if (!Array.isArray(data) || !data.length) return;

                        data.forEach(function (place) {
                            var li = document.createElement('li');
                            li.textContent = place.display_name;
                            li.addEventListener('click', function () {
                                input.value = place.display_name;
                                var cityInput = document.querySelector('input[name="bar-city"]');
                                if (cityInput && place.address) {
                                    var detectedCity = place.address.city || place.address.town || place.address.village || place.address.county || '';
                                    if (detectedCity) {
                                        cityInput.value = detectedCity;
                                    }
                                }
                                if (mapIframe && place.lat && place.lon) {
                                    var lat = parseFloat(place.lat);
                                    var lon = parseFloat(place.lon);
                                    if (!isNaN(lat) && !isNaN(lon)) {
                                        var delta = 0.006;
                                        var bbox = [lon - delta, lat - delta, lon + delta, lat + delta].join('%2C');
                                        mapIframe.src = 'https://www.openstreetmap.org/export/embed.html?bbox=' + bbox + '&layer=mapnik&marker=' + encodeURIComponent(lat + ',' + lon);
                                    }
                                }
                                list.innerHTML = '';
                            });
                            list.appendChild(li);
                        });
                    })
                    .catch(function () {
                        list.innerHTML = '';
                    });
            }, 250);
        });

        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) {
                list.innerHTML = '';
            }
        });
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'prcw_official_bars_script' );

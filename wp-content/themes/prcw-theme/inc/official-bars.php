<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PRCW_BAR_NEW_PRODUCT_ID', 143 );
define( 'PRCW_BAR_RENEWAL_PRODUCT_ID', 144 );

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
    if ( isset( $_GET['add-to-cart'] ) ) {
        $product_id = intval( $_GET['add-to-cart'] );
        if ( in_array( $product_id, array( PRCW_BAR_NEW_PRODUCT_ID, PRCW_BAR_RENEWAL_PRODUCT_ID ), true ) ) {
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
    $locationiq_key = prcw_get_locationiq_api_key();
    ?>
    <script>
    document.addEventListener('wpcf7mailsent', function (event) {
        var form = event.target;
        var selected = form.querySelector('input[name="subscription-type"]:checked');

        if (!selected) {
            return; // no es nuestro formulario, no hacemos nada
        }

        var productId = null;

        if (selected.value === 'New Subscription') {
            productId = <?php echo PRCW_BAR_NEW_PRODUCT_ID; ?>;
        } else if (selected.value === 'Subscription Renewal') {
            productId = <?php echo PRCW_BAR_RENEWAL_PRODUCT_ID; ?>;
        }

        if (productId) {
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
        var locationIqKey = '<?php echo esc_js( $locationiq_key ); ?>';
        if (!locationIqKey) {
            console.warn('PRCW: LocationIQ API key no configurada. Define PRCW_LOCATIONIQ_API_KEY en wp-config.php o vía filtro prcw_locationiq_api_key.');
            return;
        }
        var input = document.querySelector('input[name="bar-address"]');
        if (!input) return;

        var wrapper = document.createElement('div');
        wrapper.className = 'prcw-address-autocomplete';
        input.parentNode.insertBefore(wrapper, input.nextSibling);
        wrapper.appendChild(input);

        var list = document.createElement('ul');
        list.className = 'prcw-address-suggestions';
        wrapper.appendChild(list);

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

fetch('https://api.locationiq.com/v1/autocomplete?key=' + encodeURIComponent(locationIqKey) + '&q=' + encodeURIComponent(query) + '&limit=5', { signal: controller.signal })
    .then(function (res) {
        clearTimeout(timeoutId);
        if (!res.ok) {
            return []; // sin resultados (404) u otro error — mostramos lista vacía en vez de romper
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
            list.innerHTML = '';
        });
        list.appendChild(li);
    });
})                    .catch(function () {
                        list.innerHTML = '';
                    });
            }, 250); // espera 400ms sin escribir antes de buscar, para no saturar la API
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

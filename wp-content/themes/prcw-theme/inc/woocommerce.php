<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cambia el texto del botón "Add to cart" a "Add Donation"
 * solo para el producto de Tip Jar (reemplaza 123 por el ID real)
 */
function prcw_tip_jar_button_text( $text, $product ) {
    $tip_jar_product_id = 327;
    if ( $product->get_id() === $tip_jar_product_id ) {
        return __( 'ADD DONATION', 'prcw-theme' );
    }
    return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'prcw_tip_jar_button_text', 10, 2 );
add_filter( 'woocommerce_product_add_to_cart_text', 'prcw_tip_jar_button_text', 10, 2 );

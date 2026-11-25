<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cambia el texto del botón "Add to cart" a "Add Donation"
 * solo para el producto de Tip Jar configurado en PRCW Settings (fallback: 327)
 */
function prcw_tip_jar_button_text( $text, $product ) {
    if ( ! class_exists( 'WooCommerce' ) || ! is_object( $product ) || ! method_exists( $product, 'get_id' ) ) {
        return $text;
    }

    $tip_jar_product_id = function_exists( 'prcw_get_tip_jar_product_id' ) ? prcw_get_tip_jar_product_id() : 327;
    if ( $tip_jar_product_id > 0 && (int) $product->get_id() === $tip_jar_product_id ) {
        return __( 'ADD DONATION', 'prcw-theme' );
    }

    return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'prcw_tip_jar_button_text', 10, 2 );
add_filter( 'woocommerce_product_add_to_cart_text', 'prcw_tip_jar_button_text', 10, 2 );

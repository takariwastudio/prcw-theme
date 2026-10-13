<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme setup
 */
function prcw_theme_setup() {
    // Soporte para bloques y FSE
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );

    // Logo personalizable
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Traducciones
    load_theme_textdomain( 'prcw-theme', PRCW_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'prcw_theme_setup' );

/**
 * Carga las fuentes locales (Futura PT / Futura PT Condensed)
 */
function prcw_enqueue_fonts() {
    wp_enqueue_style(
        'prcw-fonts',
        PRCW_THEME_URI . '/assets/fonts/fonts.css',
        array(),
        PRCW_THEME_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'prcw_enqueue_fonts' );
add_action( 'enqueue_block_editor_assets', 'prcw_enqueue_fonts' ); // para que se vea igual en el editor

function prcw_enqueue_header_styles() {
    wp_enqueue_style(
        'prcw-header',
        PRCW_THEME_URI . '/assets/css/header.css',
        array(),
        PRCW_THEME_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'prcw_enqueue_header_styles' );

/**
 * WooCommerce auto-inserta el bloque "Mi Cuenta" después del bloque Navigation
 * en temas FSE (Block Hooks). Lo desactivamos porque no está en el diseño
 * y ya manejamos el carrito manualmente en el header.
 */
function prcw_remove_customer_account_hook( $hooked_block_types, $relative_position, $anchor_block, $context ) {
    if ( 'core/navigation' === $anchor_block && 'after' === $relative_position ) {
        $hooked_block_types = array_diff( $hooked_block_types, array( 'woocommerce/customer-account' ) );
    }
    return $hooked_block_types;
}
add_filter( 'hooked_block_types', 'prcw_remove_customer_account_hook', 10, 4 );

/**
 * Sistema global de animación al hacer scroll — aplica automáticamente
 * a CUALQUIER bloque prcw/* (sin tocar cada render.php individualmente).
 */
function prcw_enqueue_scroll_animations() {
    wp_enqueue_style(
        'prcw-animations',
        PRCW_THEME_URI . '/assets/css/animations.css',
        array(),
        PRCW_THEME_VERSION
    );

    wp_enqueue_script(
        'prcw-scroll-animations',
        PRCW_THEME_URI . '/assets/js/scroll-animations.js',
        array(),
        PRCW_THEME_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'prcw_enqueue_scroll_animations' );

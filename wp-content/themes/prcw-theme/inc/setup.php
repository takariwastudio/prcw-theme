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

/**
 * Renderiza un formulario de Contact Form 7 de forma segura.
 * Evita warnings si el campo devuelve ID/array y evita imprimir el shortcode crudo si CF7 está inactivo.
 */
function prcw_render_cf7_form( $form ) {
    if ( empty( $form ) || ! shortcode_exists( 'contact-form-7' ) ) {
        return '';
    }

    $form_id = 0;
    if ( is_object( $form ) && isset( $form->ID ) ) {
        $form_id = absint( $form->ID );
    } elseif ( is_array( $form ) && isset( $form['ID'] ) ) {
        $form_id = absint( $form['ID'] );
    } elseif ( is_numeric( $form ) ) {
        $form_id = absint( $form );
    }

    if ( ! $form_id ) {
        return '';
    }

    return do_shortcode( sprintf( '[contact-form-7 id="%d"]', $form_id ) );
}

/**
 * Aviso informativo en WP Admin si faltan plugins requeridos por el theme.
 */
function prcw_missing_dependencies_admin_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    $missing = array();

    if ( ! function_exists( 'get_field' ) || ! function_exists( 'acf_register_block_type' ) ) {
        $missing[] = 'Advanced Custom Fields PRO';
    }
    if ( ! class_exists( 'WooCommerce' ) ) {
        $missing[] = 'WooCommerce';
    }
    if ( ! class_exists( 'WPCF7' ) && ! shortcode_exists( 'contact-form-7' ) ) {
        $missing[] = 'Contact Form 7';
    }

    if ( empty( $missing ) ) {
        return;
    }

    printf(
        '<div class="notice notice-warning"><p><strong>PRCW Theme:</strong> %s <code>%s</code>.</p></div>',
        esc_html__( 'Para el funcionamiento completo del sitio activa:', 'prcw-theme' ),
        esc_html( implode( ', ', $missing ) )
    );
}
add_action( 'admin_notices', 'prcw_missing_dependencies_admin_notice' );

/**
 * Purga automáticamente cachés de WordPress / SiteGround / plugins de caché
 * la primera vez que se ejecuta una nueva versión del theme.
 */
function prcw_maybe_purge_caches_on_version_change() {
    if ( get_option( 'prcw_flushed_version' ) === PRCW_THEME_VERSION ) {
        return;
    }

    update_option( 'prcw_flushed_version', PRCW_THEME_VERSION, true );

    if ( function_exists( 'wp_clean_themes_cache' ) ) {
        wp_clean_themes_cache();
    }
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
    if ( function_exists( 'sg_cachepress_purge_everything' ) ) {
        sg_cachepress_purge_everything();
    } elseif ( function_exists( 'sg_cachepress_purge_cache' ) ) {
        sg_cachepress_purge_cache();
    }
}
add_action( 'init', 'prcw_maybe_purge_caches_on_version_change', 20 );

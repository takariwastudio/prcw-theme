<?php
/**
 * PRCW Theme functions and definitions
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PRCW_THEME_VERSION', '1.0.0' );
define( 'PRCW_THEME_DIR', get_template_directory() );
define( 'PRCW_THEME_URI', get_template_directory_uri() );

// Carga modular: cada pieza del theme vive en su propio archivo dentro de /inc
require_once PRCW_THEME_DIR . '/inc/setup.php';
require_once PRCW_THEME_DIR . '/inc/acf-blocks.php';
require_once PRCW_THEME_DIR . '/inc/acf-json.php';
require_once PRCW_THEME_DIR . '/inc/acf-options.php';
require_once PRCW_THEME_DIR . '/inc/woocommerce.php';
require_once PRCW_THEME_DIR . '/inc/icon-library.php';
require_once PRCW_THEME_DIR . '/inc/official-bars.php';
require_once PRCW_THEME_DIR . '/inc/event-years.php';

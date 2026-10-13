<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function prcw_acf_json_save_point( $path ) {
    return PRCW_THEME_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'prcw_acf_json_save_point' );

function prcw_acf_json_load_point( $paths ) {
    unset( $paths[0] );
    $paths[] = PRCW_THEME_DIR . '/acf-json';
    return $paths;
}
add_filter( 'acf/settings/load_json', 'prcw_acf_json_load_point' );

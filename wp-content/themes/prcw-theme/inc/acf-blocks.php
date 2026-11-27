<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registra la categoría "PRCW Blocks" en el inserter de bloques,
 * para que todos los bloques custom aparezcan agrupados.
 */
function prcw_block_categories( $categories ) {
    return array_merge(
        array(
            array(
                'slug'  => 'prcw-blocks',
                'title' => __( 'PRCW Blocks', 'prcw-theme' ),
                'icon'  => 'layout',
            ),
        ),
        $categories
    );
}
add_filter( 'block_categories_all', 'prcw_block_categories' );

/**
 * Asigna PRCW_THEME_VERSION a todos los bloques prcw/* para invalidar caché de CSS/JS al actualizar el theme.
 */
function prcw_inject_block_version( $metadata ) {
    if ( ! empty( $metadata['name'] ) && str_starts_with( (string) $metadata['name'], 'prcw/' ) && empty( $metadata['version'] ) ) {
        $metadata['version'] = PRCW_THEME_VERSION;
    }
    return $metadata;
}
add_filter( 'block_type_metadata', 'prcw_inject_block_version' );

/**
 * Registra automáticamente todos los bloques ACF dentro de /build/blocks.
 */
function prcw_register_acf_blocks() {
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    $blocks_dir = PRCW_THEME_DIR . '/build/blocks';

    if ( ! is_dir( $blocks_dir ) ) {
        return;
    }

    $block_folders = glob( $blocks_dir . '/*', GLOB_ONLYDIR );
    if ( empty( $block_folders ) || ! is_array( $block_folders ) ) {
        return;
    }

    foreach ( $block_folders as $block_folder ) {
        if ( file_exists( $block_folder . '/block.json' ) ) {
            register_block_type( $block_folder );
        }
    }
}
add_action( 'init', 'prcw_register_acf_blocks' );

/**
 * Evita que plugins como SiteGround Speed Optimizer (sg-cachepress) re-minifiquen o combinen
 * de forma incompatible los bundles ya compilados por webpack (build/blocks/*).
 */
function prcw_exclude_block_styles_from_sgo( $exclude_list ) {
    $exclude_list = is_array( $exclude_list ) ? $exclude_list : array();
    $wp_styles    = wp_styles();

    if ( $wp_styles && ! empty( $wp_styles->registered ) ) {
        foreach ( array_keys( $wp_styles->registered ) as $handle ) {
            if ( str_starts_with( (string) $handle, 'wp-block-prcw-' ) || str_starts_with( (string) $handle, 'prcw-' ) ) {
                $exclude_list[] = $handle;
            }
        }
    }

    return array_values( array_unique( $exclude_list ) );
}
add_filter( 'sgo_css_combine_exclude', 'prcw_exclude_block_styles_from_sgo' );
add_filter( 'sgo_css_minify_exclude', 'prcw_exclude_block_styles_from_sgo' );

function prcw_exclude_block_scripts_from_sgo( $exclude_list ) {
    $exclude_list = is_array( $exclude_list ) ? $exclude_list : array();
    $wp_scripts   = wp_scripts();

    if ( $wp_scripts && ! empty( $wp_scripts->registered ) ) {
        foreach ( array_keys( $wp_scripts->registered ) as $handle ) {
            if ( str_starts_with( (string) $handle, 'prcw-' ) ) {
                $exclude_list[] = $handle;
            }
        }
    }

    return array_values( array_unique( $exclude_list ) );
}
add_filter( 'sgo_js_minify_exclude', 'prcw_exclude_block_scripts_from_sgo' );
add_filter( 'sgo_javascript_combine_exclude', 'prcw_exclude_block_scripts_from_sgo' );

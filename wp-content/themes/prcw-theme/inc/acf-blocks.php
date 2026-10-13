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
 * Registra automáticamente todos los bloques ACF dentro de /blocks
 * Así, cada vez que crees una nueva carpeta con su block.json,
 * NO hay que tocar este archivo — se registra sola.
 */
function prcw_register_acf_blocks() {
    $blocks_dir = PRCW_THEME_DIR . '/build/blocks';

    if ( ! is_dir( $blocks_dir ) ) {
        return;
    }

    $block_folders = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

    foreach ( $block_folders as $block_folder ) {
        if ( file_exists( $block_folder . '/block.json' ) ) {
            register_block_type( $block_folder );
        }
    }
}
add_action( 'init', 'prcw_register_acf_blocks' );

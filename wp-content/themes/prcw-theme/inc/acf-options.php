<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page( array(
        'page_title' => 'Footer Settings',
        'menu_title' => 'Footer Settings',
        'menu_slug'  => 'footer-settings',
        'capability' => 'edit_posts',
        'icon_url'   => 'dashicons-editor-insertmore',
        'position'   => 60,
    ) );

    acf_add_options_page( array(
        'page_title' => 'PRCW Settings',
        'menu_title' => 'PRCW Settings',
        'menu_slug'  => 'prcw-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-settings',
        'position'   => 61,
    ) );
}

// Registra campo API Key programáticamente para que get_field('locationiq_api_key','option') funcione sin crear el field manualmente en ACF UI
if ( function_exists( 'acf_add_local_field_group' ) ) {
    add_action( 'acf/init', function() {
        acf_add_local_field_group( array(
            'key' => 'group_prcw_api_settings',
            'title' => 'PRCW API Settings',
            'fields' => array(
                array(
                    'key' => 'field_locationiq_api_key',
                    'label' => 'LocationIQ API Key',
                    'name' => 'locationiq_api_key',
                    'type' => 'text',
                    'instructions' => 'Define aquí la key de LocationIQ o usa la constante PRCW_LOCATIONIQ_API_KEY en wp-config.php / .wp-env.json (config.PRCW_LOCATIONIQ_API_KEY). Rotar la key si fue expuesta en git.',
                    'required' => 0,
                    'wrapper' => array( 'width' => '100' ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'prcw-settings',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ) );
    } );
}

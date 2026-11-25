<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'acf/init', function() {
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

    // Registra campos de PRCW Settings programáticamente para que funcionen sin crearlos manualmente en ACF UI
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key'   => 'group_prcw_api_settings',
            'title' => 'PRCW API Settings',
            'fields' => array(
                array(
                    'key'          => 'field_locationiq_api_key',
                    'label'        => 'LocationIQ API Key',
                    'name'         => 'locationiq_api_key',
                    'type'         => 'text',
                    'instructions' => 'Define aquí la key de LocationIQ o usa la constante PRCW_LOCATIONIQ_API_KEY en wp-config.php / .wp-env.json (config.PRCW_LOCATIONIQ_API_KEY). Rotar la key si fue expuesta en git.',
                    'required'     => 0,
                    'wrapper'      => array( 'width' => '50' ),
                ),
                array(
                    'key'          => 'field_prcw_github_token',
                    'label'        => 'GitHub Token (Opcional — Auto-Updater)',
                    'name'         => 'github_token',
                    'type'         => 'password',
                    'instructions' => 'Opcional mientras takariwastudio/prcw-theme sea público. Necesario solo si el repo pasa a privado o para subir el límite de peticiones de GitHub API (o usa PRCW_GITHUB_TOKEN en wp-config.php).',
                    'required'     => 0,
                    'wrapper'      => array( 'width' => '50' ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'prcw-settings',
                    ),
                ),
            ),
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
        ) );

        acf_add_local_field_group( array(
            'key'   => 'group_prcw_woocommerce_settings',
            'title' => 'WooCommerce Product IDs',
            'fields' => array(
                array(
                    'key'           => 'field_prcw_bar_new_product_id',
                    'label'         => 'Official Bars — New Subscription Product ID',
                    'name'          => 'bar_new_product_id',
                    'type'          => 'number',
                    'instructions'  => 'ID del producto en WooCommerce para nueva suscripción de Official Bars (fallback: 143).',
                    'required'      => 0,
                    'default_value' => 143,
                    'min'           => 1,
                    'step'          => 1,
                    'wrapper'       => array( 'width' => '33' ),
                ),
                array(
                    'key'           => 'field_prcw_bar_renewal_product_id',
                    'label'         => 'Official Bars — Subscription Renewal Product ID',
                    'name'          => 'bar_renewal_product_id',
                    'type'          => 'number',
                    'instructions'  => 'ID del producto en WooCommerce para renovación de Official Bars (fallback: 144).',
                    'required'      => 0,
                    'default_value' => 144,
                    'min'           => 1,
                    'step'          => 1,
                    'wrapper'       => array( 'width' => '33' ),
                ),
                array(
                    'key'           => 'field_prcw_tip_jar_product_id',
                    'label'         => 'Tip Jar (Donation) Product ID',
                    'name'          => 'tip_jar_product_id',
                    'type'          => 'number',
                    'instructions'  => 'ID del producto en WooCommerce para donaciones / Tip Jar (fallback: 327).',
                    'required'      => 0,
                    'default_value' => 327,
                    'min'           => 1,
                    'step'          => 1,
                    'wrapper'       => array( 'width' => '34' ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'prcw-settings',
                    ),
                ),
            ),
            'menu_order'            => 10,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
        ) );
    }
} );

/**
 * Resuelve un ID de producto de WooCommerce configurable en PRCW Settings.
 * Prioridad: constante en wp-config.php > ACF option (PRCW Settings) > wp_options > default > filtro.
 */
function prcw_resolve_product_id( $field_name, $constant_name, $filter_name, $default_id ) {
    $resolved = 0;

    if ( $constant_name && defined( $constant_name ) && absint( constant( $constant_name ) ) > 0 ) {
        $resolved = absint( constant( $constant_name ) );
    } elseif ( function_exists( 'get_field' ) ) {
        $acf_value = get_field( $field_name, 'option' );
        if ( is_object( $acf_value ) && isset( $acf_value->ID ) ) {
            $resolved = absint( $acf_value->ID );
        } elseif ( is_array( $acf_value ) && isset( $acf_value['ID'] ) ) {
            $resolved = absint( $acf_value['ID'] );
        } elseif ( is_numeric( $acf_value ) && absint( $acf_value ) > 0 ) {
            $resolved = absint( $acf_value );
        }
    }

    if ( ! $resolved ) {
        $raw_option = get_option( 'options_' . $field_name );
        if ( is_numeric( $raw_option ) && absint( $raw_option ) > 0 ) {
            $resolved = absint( $raw_option );
        }
    }

    if ( ! $resolved ) {
        $resolved = absint( $default_id );
    }

    return absint( apply_filters( $filter_name, $resolved ) );
}

function prcw_get_bar_new_product_id() {
    return prcw_resolve_product_id( 'bar_new_product_id', 'PRCW_BAR_NEW_PRODUCT_ID', 'prcw_bar_new_product_id', 143 );
}

function prcw_get_bar_renewal_product_id() {
    return prcw_resolve_product_id( 'bar_renewal_product_id', 'PRCW_BAR_RENEWAL_PRODUCT_ID', 'prcw_bar_renewal_product_id', 144 );
}

function prcw_get_tip_jar_product_id() {
    return prcw_resolve_product_id( 'tip_jar_product_id', 'PRCW_TIP_JAR_PRODUCT_ID', 'prcw_tip_jar_product_id', 327 );
}

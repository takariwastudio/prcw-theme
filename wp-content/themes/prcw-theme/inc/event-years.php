<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function prcw_register_event_year_cpt() {
    register_post_type( 'prcw_event_year', array(
        'labels' => array(
            'name' => 'Event Years',
            'singular_name' => 'Event Year',
            'add_new_item' => 'Add New Event Year',
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'events' ),
        'supports' => array( 'title'),
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_rest' => true,
    ));
}
add_action( 'init', 'prcw_register_event_year_cpt' );

function prcw_get_dropbox_photos( $folder_path, $random_count = null) {
    return array();
}

function prcw_get_event_years_split() {
    $query = new WP_Query( array(
        'post_type' => 'prcw_event_year',
        'posts_per_page' => -1,
        'meta_key' => 'year',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ));

    $all = $query->posts;

    return array(
        'active' => array_slice( $all, 0, 3 ),
        'archive' => array_slice( $all, 3 ),
    );
}
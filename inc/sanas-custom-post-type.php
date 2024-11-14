<?php
if (!defined('ABSPATH')) {
    exit;
}
// Register the custom post type
function sanas_custom_post_types() {

    register_post_type('sanas_card',
        array(
            'labels'        => array(
                'name'                  => esc_html__('Card', 'sanas-core'),
                'singular_name'         => esc_html__('Card', 'sanas-core'),
                'menu_name'             => esc_html__('Card', 'sanas-core'),
                'add_new'               => esc_html__('Add Card', 'sanas-core'),
                'add_new_item'          => esc_html__('Add New Card', 'sanas-core'),
                'all_items'             => esc_html__('All Card', 'sanas-core'),
                'edit'                  => esc_html__('Edit Card', 'sanas-core'),
                'edit_item'             => esc_html__('Edit Card', 'sanas-core'),
                'featured_image'        => esc_html__('Card Image', 'sanas-core'),
                'set_featured_image'    => esc_html__('Set Card Image', 'sanas-core'),
                'remove_featured_image' => esc_html__('Remove Card Image', 'sanas-core'),
                'use_featured_image'    => esc_html__('Use as Card Image', 'sanas-core'),
                'not_found'             => esc_html__('No Card Found', 'sanas-core'),
                'not_found_in_trash'    => esc_html__('No Card Found in Trash', 'sanas-core'),
                'parent'                => esc_html__('Parent Card', 'sanas-core'),
            ),
            'rewrite' => array(
                'slug'       => 'card',
                'with_front' => true
            ),
            'label' => esc_html__('Card', 'sanas-core'),
            'public'        => true,
            'show_ui'       => true,
            'show_in_rest'  => false,
            'show_in_menu'  => true,
            'menu_icon'     => 'dashicons-index-card',
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
        )
    );
    $category_label = array(
        'name'          => esc_html__('Card Categories', 'sanas-core'),
        'singular_name' => esc_html__('Card Category', 'sanas-core'),
        'menu_name'     => esc_html__('Card Categories', 'sanas-core'),
        'all_items'     => esc_html__('All Card', 'sanas-core'),
        'back_to_items' => esc_html__('&larr; Go to Card Categories', 'sanas-core')
    );
    $category_args = array(
        'labels'        => $category_label,
        'show_ui'       => true,
        'hierarchical'  => true
    );
    register_taxonomy('sanas-card-category', 'sanas_card', $category_args);
    register_post_type('sanas_rsvp',
        array(
            'labels'        => array(
                'name'                  => esc_html__('Rsvp','sanas-core'),
                'singular_name'         => esc_html__('Rsvp','sanas-core'),
                'menu_name'             => esc_html__('Rsvp','sanas-core'),
                'add_new'               => esc_html__('Add Rsvp','sanas-core'),
                'add_new_item'          => esc_html__('Add New Rsvp','sanas-core'),
                'all_items'             => esc_html__('All Rsvp','sanas-core'),
                'edit'                  => esc_html__('Edit Rsvp','sanas-core'),
                'edit_item'             => esc_html__('Edit Rsvp','sanas-core'),
                'featured_image'        => esc_html__('Rsvp Image','sanas-core'),
                'set_featured_image'    => esc_html__('Set Rsvp Image','sanas-core'),
                'remove_featured_image' => esc_html__('Remove Rsvp Image','sanas-core'),
                'use_featured_image'    => esc_html__('Use as Rsvp Image','sanas-core'),
                'not_found'             => esc_html__('No Rsvp Found','sanas-core'),
                'not_found_in_trash'    => esc_html__('No Rsvp Found in Trash','sanas-core'),
                'parent'                => esc_html__('Parent Rsvp','sanas-core'),
            ),
            'rewrite'      => array(
                'slug'       => 'Rsvp',
                'with_front' => true
            ),
            'label' => esc_html__('Rsvp','sanas-core'),
            'public'        => true,
            'show_ui'       => true,
            'show_in_rest'  => false,
            'show_in_menu'  => true,
            'menu_icon'     => 'dashicons-megaphone',
            'supports'      => array('title','editor','thumbnail','excerpt'),
        )
    );
}
add_action('init', 'sanas_custom_post_types');
// Add Canvas Editor Meta Box

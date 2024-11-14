<?php

add_action('admin_init', 'tg_metabox_add_rsvp_details');
add_action('admin_head-post.php', 'tg_metabox_rsvp_details_print_scripts');
add_action('admin_head-post-new.php', 'tg_metabox_rsvp_details_print_scripts');
add_action('save_post', 'tg_metabox_update_rsvp_details', 10, 2);

if (!function_exists('tg_metabox_add_rsvp_details')) :
function tg_metabox_add_rsvp_details() {
    add_meta_box(
        'rsvp_details',
        'RSVP Details',
        'tg_metabox_item_rsvp_details',
        'sanas_rsvp',
        'normal',
        'core'
    );
}
endif;

if (!function_exists('tg_metabox_item_rsvp_details')) :
function tg_metabox_item_rsvp_details() {
    global $post;
    $guest_name = get_post_meta($post->ID, 'guest_name', true);
    $event_name = get_post_meta($post->ID, 'event_name', true);
    $event_date = get_post_meta($post->ID, 'event_date', true);
    

    $guest_contact = get_post_meta($post->ID, 'guest_contact', true);
    $guest_message = get_post_meta($post->ID, 'guest_message', true);

    wp_nonce_field('rsvp_details_nonce_action', 'rsvp_details_nonce');
    ?>
    <div id="rsvp-details-section">
        <div class="form-field">
            <label for="event-title">Event Title</label>
            <input type="text" id="event-title" name="event_title" value="<?php echo esc_attr($event_name); ?>" style="width: 100%;">
        </div>
         <div class="form-field">
            <label for="event-date">Event Date</label>
            <input type="text" id="event-date" name="event_date" value="<?php echo esc_attr($event_date); ?>" style="width: 100%;">
        </div>
        <div class="form-field">
            <label for="guest-name">Guest Name</label>
            <input type="text" id="guest-name" name="guest_name" value="<?php echo esc_attr($guest_name); ?>" style="width: 100%;">
        </div>
        <div class="form-field">
            <label for="guest-contact">Guest Contact Number</label>
            <input type="text" id="guest-contact" name="guest_contact" value="<?php echo esc_attr($guest_contact); ?>" style="width: 100%;">
        </div>
        <div class="form-field">
            <label for="guest-message">RSVP Message</label>
            <textarea id="guest-message" name="guest_message" rows="4" style="width: 100%;"><?php echo esc_textarea($guest_message); ?></textarea>
        </div>
    </div>
    <?php
}
endif;

if (!function_exists('tg_metabox_rsvp_details_print_scripts')) :
function tg_metabox_rsvp_details_print_scripts() {
    global $post;
    if ('sanas_rsvp' != $post->post_type) 
        return;
    
    wp_enqueue_media();
    wp_enqueue_style('sanas-rsvp', plugin_dir_url(__FILE__) . 'css/sanas-rsvp.css'); 

}
endif;

if (!function_exists('tg_metabox_update_rsvp_details')) :
function tg_metabox_update_rsvp_details($post_id, $post_object) {
    if (!isset($_POST['rsvp_details_nonce']) || !wp_verify_nonce($_POST['rsvp_details_nonce'], 'rsvp_details_nonce_action')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['guest_name'])) {
        update_post_meta($post_id, 'guest_name', sanitize_text_field($_POST['guest_name']));
    }

    if (isset($_POST['guest_contact'])) {
        update_post_meta($post_id, 'guest_contact', sanitize_text_field($_POST['guest_contact']));
    }

    if (isset($_POST['guest_message'])) {
        update_post_meta($post_id, 'guest_message', wp_kses_post($_POST['guest_message']));
    }
}
endif;
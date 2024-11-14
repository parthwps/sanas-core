<?php
add_action('admin_init', 'tg_metabox_add_item_video_gallery');
add_action('admin_head-post.php', 'tg_metabox_video_print_scripts');
add_action('admin_head-post-new.php', 'tg_metabox_video_print_scripts');
add_action('save_post', 'tg_metabox_update_item_video', 10, 2);

if (!function_exists('tg_metabox_add_item_video_gallery')) : 
function tg_metabox_add_item_video_gallery() {
    add_meta_box(
        'video_gallery',
        'Video Library',
        'tg_metabox_item_video_gallery',
        'sanas_rsvp',
        'normal',
        'core'
    );
}
endif;

if (!function_exists('tg_metabox_item_video_gallery')) :  
function tg_metabox_item_video_gallery() {
    global $post;
    $video_url = get_post_meta($post->ID, 'opt_upload_video', true);
    wp_nonce_field('opt_upload_video_nonce_action', 'opt_upload_video_nonce');
    ?>
    <div id="video-upload-section">
        <input type="url" id="video-url" name="opt_upload_video" value="<?php echo esc_attr($video_url); ?>" style="width: 100%;">
        <button id="upload-video-button" class="button"><?php _e('Upload Video', 'textdomain'); ?></button>
        <button id="delete-video-button" class="button" style="display: <?php echo $video_url ? 'inline-block' : 'none'; ?>;"><?php _e('Delete Video', 'textdomain'); ?></button>
        <video id="video-preview" controls style="display: <?php echo $video_url ? 'block' : 'none'; ?>; width: 100%;" src="<?php echo esc_url($video_url); ?>"></video>
    </div>
    <?php
}
endif;

if (!function_exists('tg_metabox_video_print_scripts')) : 
function tg_metabox_video_print_scripts() {
    global $post;
    if ('sanas_rsvp' != $post->post_type) 
        return;
    
    wp_enqueue_media();
    wp_enqueue_style( 'sanas-rsvp', plugin_dir_url( __FILE__ ) .'css/sanas-rsvp.css');  

    wp_enqueue_script('video-preview', plugin_dir_url(__FILE__) . 'js/video-preview.js', array('jquery'), '1.0', true);
    wp_localize_script('video-preview', 'videoPreviewVars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'delete_nonce' => wp_create_nonce('delete_video_nonce'),
    ));
}
endif;

if (!function_exists('tg_metabox_update_item_video')) :  
function tg_metabox_update_item_video($post_id, $post_object) {
    if (!isset($_POST['opt_upload_video_nonce']) || !wp_verify_nonce($_POST['opt_upload_video_nonce'], 'opt_upload_video_nonce_action')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['opt_upload_video'])) {
        update_post_meta($post_id, 'opt_upload_video', sanitize_text_field($_POST['opt_upload_video']));
    } else {
        delete_post_meta($post_id, 'opt_upload_video');
    }
}
endif;


add_action('wp_ajax_sanas_delete_video', 'sanas_delete_video');
add_action('wp_ajax_nopriv_sanas_delete_video', 'sanas_delete_video');

if (!function_exists('sanas_delete_video')) {
    function sanas_delete_video() {
        check_ajax_referer('delete_video_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error();
        }

        delete_post_meta($post_id, 'opt_upload_video');
        wp_send_json_success();
    }
}
?>

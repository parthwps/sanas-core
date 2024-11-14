<?php
add_action( 'admin_init', 'tg_metabox_add_registry_details' );
add_action( 'admin_head-post.php', 'tg_metabox_registry_details_scripts' );
add_action( 'admin_head-post-new.php', 'tg_metabox_registry_details_scripts' );
add_action( 'save_post', 'tg_metabox_update_registry_details', 10, 2 );

/**
 * Add custom Meta Box to sanas_rsvp post type
 */
function tg_metabox_add_registry_details() {
    add_meta_box(
        'registry_details',
        'Registry Details',
        'tg_metabox_registry_details_call',
        'sanas_rsvp', // Replace with your post type
        'normal',
        'core'
    );
}

/**
 * Print the Meta Box content
 */
function tg_metabox_registry_details_call( $post ) {
    // Use nonce for verification
    wp_nonce_field( basename( __FILE__ ), 'registry_details_meta_box_nonce' );

    // Retrieve existing values from the database
    $registries = get_post_meta( $post->ID, 'registries', true );
    $registries = is_array( $registries ) ? $registries : array();
    // print_r($registries);

    ?>
    <div class="registry-details">
        <div id="registry-container">
            <?php foreach ( $registries as $index => $registry ) : ?>
                <div class="registry-item">
                    <div class="form-group">
                        <label for="registry_name_<?php echo $index; ?>">Registry Name</label>
                        <input type="text" id="registry_name_<?php echo $index; ?>" name="registries[<?php echo $index; ?>][name]" class="form-control" value="<?php echo esc_attr( $registry['name'] ); ?>">
                    </div>
                    <div class="form-group">
                        <label for="registry_url_<?php echo $index; ?>">Registry URL</label>
                        <input type="url" id="registry_url_<?php echo $index; ?>" name="registries[<?php echo $index; ?>][url]" class="form-control" value="<?php echo esc_url( $registry['url'] ); ?>">
                    </div>
                    <button type="button" class="btn btn-default remove_registry">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="add-realwedding-credits">
            <button type="button" class="btn btn-default" id="add_new_registry">+ Add Registry</button>
        </div>
    </div>
    <?php
}

/**
 * Print styles and scripts
 */
function tg_metabox_registry_details_scripts() {
    // Check for correct post_type
    global $post;
    if ( 'sanas_rsvp' != $post->post_type )
        return;

    wp_enqueue_style( 'admin-style', plugin_dir_url( __FILE__ ) .'css/sanas-rsvp.css', array(), '1.0' );
    wp_enqueue_script( 'registry-details-scripts', plugin_dir_url( __FILE__ ) .'js/registry-details-scripts.js', array('jquery'), '1.0', true );
}

/**
 * Save post action, process fields
 */ 
function tg_metabox_update_registry_details( $post_id, $post ) {
    // Doing revision, exit earlier
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
        return;
    
    // Verify authenticity
    if ( ! isset( $_POST['registry_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['registry_details_meta_box_nonce'], basename( __FILE__ ) ) )
        return;

    // Correct post type
    if ( 'sanas_rsvp' != $post->post_type )
        return;



    // Update registry details
    if ( isset( $_POST['registries'] ) && is_array( $_POST['registries'] ) ) {
        $registries = array_map( function( $registry ) {
            return array(
                'name' => sanitize_text_field( $registry['name'] ),
                'url'  => esc_url_raw( $registry['url'] )
            );
        }, $_POST['registries'] );
        update_post_meta( $post_id, 'registries', $registries );
    } else {
        delete_post_meta( $post_id, 'registries' );
    }
}
?>

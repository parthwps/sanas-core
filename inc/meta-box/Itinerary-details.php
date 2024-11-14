<?php
add_action( 'admin_init', 'tg_metabox_add_ltinerary_details' );
add_action( 'admin_head-post.php', 'tg_metabox_ltinerary_details_scripts' );
add_action( 'admin_head-post-new.php', 'tg_metabox_ltinerary_details_scripts' );
add_action( 'save_post', 'tg_metabox_update_ltinerary_details', 10, 2 );

/**
 * Add custom Meta Box to Posts post type
 */
if ( ! function_exists( 'tg_metabox_add_ltinerary_details' ) ) { 
    function tg_metabox_add_ltinerary_details() {
        add_meta_box(
            'ltinerary_details',
            'Itinerary Details',
            'tg_metabox_ltinerary_details_call',
            'sanas_rsvp', // Replace with your post type
            'normal',
            'core'
        );
    }
}

/**
 * Print the Meta Box content
 */
if ( ! function_exists( 'tg_metabox_ltinerary_details_call' ) ) {
    function tg_metabox_ltinerary_details_call( $post ) {
        // Use nonce for verification
        wp_nonce_field( basename( __FILE__ ), 'itinerary_details_meta_box_nonce' );

        // Retrieve existing values from the database
        $itinerary_details = get_post_meta( $post->ID, 'listing_itinerary_details', true );

        // Ensure $itinerary_details is an array
        if ( ! is_array( $itinerary_details ) ) {
            $itinerary_details = array();
        }
        ?>
        <div class="itinerary-details">
            <div id="program-details-container">
                <?php foreach ( $itinerary_details as $index => $details ) : ?>
                    <div class="row program-details-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="program_name[]" class="form-control" placeholder="<?php esc_html_e( 'Program Name', 'textdomain' ); ?>" value="<?php echo esc_attr( $details['program_name'] ?? '' ); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="program_time[]" class="form-control" placeholder="<?php esc_html_e( 'Program Time', 'textdomain' ); ?>" value="<?php echo esc_attr( $details['program_time'] ?? '' ); ?>">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="remove-program btn btn-danger">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="add-realwedding-credits">
                <button type="button" class="btn btn-default" id="add_new_program">+ Add Program</button>
            </div>
        </div>
        <?php
    }
}

/**
 * Print styles and scripts
 */
if ( ! function_exists( 'tg_metabox_ltinerary_details_scripts' ) ) {
    function tg_metabox_ltinerary_details_scripts() {
        // Check for correct post_type
        global $post;
        if ( 'sanas_rsvp' != $post->post_type ) // Replace with your post type
            return;

        wp_enqueue_style( 'admin-style', plugin_dir_url( __FILE__ ) .'css/sanas-rsvp.css', array(), '1.0' );
        wp_enqueue_script( 'ltinerary-details-scripts', plugin_dir_url( __FILE__ ) .'js/ltinerary-details-scripts.js', array('jquery'), '1.0', true );
    }
}

/**
 * Save post action, process fields
 */ 
if ( ! function_exists( 'tg_metabox_update_ltinerary_details' ) ) {
    function tg_metabox_update_ltinerary_details( $post_id, $post ) {
        // Doing revision, exit earlier
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
            return;
        // Verify authenticity
        if ( ! isset( $_POST['itinerary_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['itinerary_details_meta_box_nonce'], basename( __FILE__ ) ) ) {
           return;
        }
        // Correct post type
        if ( 'sanas_rsvp' != $post->post_type ) // Replace with your post type
            return;

        // Update itinerary details
        $program_names = isset( $_POST['program_name'] ) ? array_map( 'sanitize_text_field', $_POST['program_name'] ) : array();
        $program_times = isset( $_POST['program_time'] ) ? array_map( 'sanitize_text_field', $_POST['program_time'] ) : array();

        $itinerary_details = array();
        foreach ( $program_names as $index => $name ) {
            if ( isset( $program_times[ $index ] ) ) {
                $itinerary_details[] = array(
                    'program_name' => $name,
                    'program_time' => $program_times[ $index ],
                );
            }
        }
        
        update_post_meta( $post_id, 'listing_itinerary_details', $itinerary_details );
    }
}
?>

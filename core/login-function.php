<?php
require 'php-library/vendor/autoload.php';
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
if (!function_exists('sanas_signin_user_status')) {
    function sanas_signin_user_status() {
        check_ajax_referer('ajax-usersignin-nonce', 'security');
        global $wpdb;
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $ajaxvalue = isset($_POST['ajaxvalue']) ? intval($_POST['ajaxvalue']) : 0;
        $response = array('loggedin' => false, 'message' => '', 'redirect_url' => '', 'inserted_id' => 0);
        $user = get_user_by('email', $email);
        $current_url = isset($_POST['current_url']) ? esc_url_raw($_POST['current_url']) : home_url();
        $backacardURL = isset($_POST['backacardURL']) ? esc_url_raw($_POST['backacardURL']) : $current_url;


        $imageUrl = $_POST['imageUrl'];
        $colorbg = $_POST['colorbg'];

        if ($ajaxvalue == 1) {
            if ($user) {   
                if (wp_check_password($password, $user->user_pass, $user->ID)) {
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                    $event_card_id = isset($_POST['card-id']) ? intval($_POST['card-id']) : 0;
                    $canvas_data = isset($_POST['canvas_data']) ? wp_slash($_POST['canvas_data']) : '';
                     $image_data = $_POST['image_data'];
                    // Check if card-id is not 0 before inserting data
                    if ($event_card_id !== 0) {
                        $wpdb->insert(
                            $wpdb->prefix . 'sanas_card_event',
                            array(
                                'event_user' => $user->ID,
                                'event_card_id' => $event_card_id,
                                'event_front_card_json_edit' => $canvas_data,
                                'event_front_card_preview' =>  $image_data,
                                'event_back_card_json_edit' => '',
                                'event_back_card_preview' => '',
                                'event_rsvp_id' => 0,
                                'event_rsvp_bg_link' => '',
                                'event_front_bg_link' => $imageUrl,
                                'event_front_bg_color' => $colorbg,
                                'event_guest_id' => 0,
                                'event_step_id' => '1',
                                'event_status' => ''
                            )
                        );
                        $response['inserted_id'] = $wpdb->insert_id;
                        $response['loggedin'] = true;
                        $response['message'] = 'Login successful and data inserted!';
                    } else {
                        $response['message'] = 'Invalid card-id. Data not inserted.';
                    }
                } else {
                    $response['message'] = 'Incorrect password.';
                }
            } else {
                $response['message'] = 'Email address not found.';
            }
        }
            else {
            if ($user) {  
                if (wp_check_password($password, $user->user_pass, $user->ID)) {
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                    $response['loggedin'] = true;
                    $response['message'] = 'Login successful!';
                    $response['redirect_url'] = $current_url;
                } else {
                    $response['message'] = 'Incorrect password.';
                }
            } else {
                $response['message'] = 'Email address not found.';
            }
        }
        echo json_encode($response);
        wp_die();
    }
    add_action('wp_ajax_nopriv_sanas_signin_user_status', 'sanas_signin_user_status');
    add_action('wp_ajax_sanas_signin_user_status', 'sanas_signin_user_status');
}
// Sanas Signup Login Popup
if (!function_exists('sanas_signup_user_status')) {
    function sanas_signup_user_status() {
        check_ajax_referer('ajax-usersignup-nonce', 'security');
        global $wpdb;
        $user_name = sanitize_text_field($_POST['username']);
        $yourname = sanitize_text_field($_POST['yourname']);
        $user_email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        //$user_dashboard_link = sanas_get_vendor_user_dashboard_link();
        $response = array('register' => false, 'message' => '', 'redirect_url' => '');

        if (!is_email($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = esc_html__('Please enter a valid email.', 'sanas');
        } elseif (empty($user_name)) {
            $response['message'] = esc_html__('Please enter a name.', 'sanas');
        } elseif (empty($user_email)) {
            $response['message'] = esc_html__('Please enter an email.', 'sanas');
        } elseif (empty($password)) {
            $response['message'] = esc_html__('Please enter a password.', 'sanas');
        } elseif (username_exists($user_name)) {
            $response['message'] = esc_html__('Username already exists. Please signin', 'sanas');
        } elseif (email_exists($user_email)) {
            $response['message'] = esc_html__('Email already exists. Please signin', 'sanas');
        } else {
            $user_id = wp_create_user($user_name, $password, $user_email);
            if (!is_wp_error($user_id)) {
                $info = array(
                    'user_login' => $user_name,
                    'user_password' => $password,
                );
                update_user_meta( $user_id, 'first_name', $yourname) ;
                $user_signon = wp_signon($info, true);
                if (!is_wp_error($user_signon)) {
                    $response['register'] = true;
                    $response['message'] = esc_html__('Account created successfully.', 'sanas');
                    $response['redirect_url'] = site_url();

                    // Retrieve email subject and body from theme options
                    $subject = sanas_options('sanas_user_signup_subject');
                    $body = sanas_options('sanas_user_signup_body');

                    // Prepare email headers
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    // Send the email
                    wp_mail($user_email, $subject, $body, $headers);
                } else {
                    $response['message'] = esc_html__('Error signing in.', 'sanas');
                }
            } else {
                $response['message'] = esc_html__('Error creating user.', 'sanas');
            }
        }
        echo json_encode($response);
        wp_die();
    }
    add_action('wp_ajax_nopriv_sanas_signup_user_status', 'sanas_signup_user_status');
    add_action('wp_ajax_sanas_signup_user_status', 'sanas_signup_user_status');
}
//Sanas Signup User verify email
if (!function_exists('sanas_verify_user_email')) {
    function sanas_verify_user_email() {

check_ajax_referer('ajax-useremail-nonce', 'security');
    $user_email = sanitize_email($_POST['email']);
    $response = array('exists' => false);
    
    if (email_exists($user_email)) {
        $response['exists'] = true;

        // Get user data by email
        $user = get_user_by('email', $user_email);

        // Generate a reset key
        $reset_key = get_password_reset_key($user);

        // Create a custom reset URL
        $reset_url = add_query_arg(
            array(
                'reset_password' => '1',
                'key' => $reset_key,
                'login' => rawurlencode($user->user_login),
            ),
            site_url() // URL of the site
        );

        // Email content
        $subject = 'Password Reset Request';
        $message = 'Hello ' . $user->user_login . ",\n\n";
        $message .= 'You have requested a password reset. Please click the following link to reset your password in a popup on our site:' . "\n\n";
        $message .= $reset_url . "\n\n";
        $message .= 'If you did not request this, please ignore this email.' . "\n\n";
        $message .= 'Thank you!' . "\n";

        // Send the email

      

        $sanas_mail_subject  = sanas_options('sanas_user_forgotpassword_subject');
        $sanas_mail_body  = sanas_options('sanas_user_forgotpassword_body');

        $website_url = site_url();
        $website_name = get_option('blogname');


        $formated_mail_content = sanas_sprintf("$sanas_mail_body", array(
            'website_url' => "$website_url",
            'website_name' => "$website_name",
            'username' => "$user->user_login",
            'forgotlink' => "$reset_url"
        ));   


        $main_body=wpautop($formated_mail_content);
        // Set content-type header to send HTML email
        $headers = array('Content-Type: text/html; charset=UTF-8');

         wp_mail($user_email, $sanas_mail_subject, $main_body,$headers);

    }

    echo json_encode($response);
    wp_die();

    }
    add_action('wp_ajax_nopriv_sanas_verify_user_email', 'sanas_verify_user_email');
    add_action('wp_ajax_sanas_verify_user_email', 'sanas_verify_user_email');
}





//Sanas User Password change code
if (!function_exists('sanas_reset_password_user')) {
    function sanas_reset_password_user() {

    check_ajax_referer('ajax-userpassword-nonce', 'security');
 
    $key = sanitize_text_field($_POST['key']);
    $login = sanitize_text_field($_POST['login']);
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];


// Check if passwords match
    if ($pass1 !== $pass2) {
        wp_send_json_error(array('message' => 'Passwords do not match.'));
    }

    $user = check_password_reset_key($key, $login);

    if (!$user || is_wp_error($user)) {
        wp_send_json_error(array('message' => 'Invalid or expired password reset link.'));
    }

    $result = reset_password($user, $pass1);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }



        $sanas_mail_subject  = sanas_options('sanas_user_resetpassword_subject');
        $sanas_mail_body  = sanas_options('sanas_user_resetpassword_body');

        $website_url = site_url();
        $website_name = get_option('blogname');


        $formated_mail_content = sanas_sprintf("$sanas_mail_body", array(
            'website_url' => "$website_url",
            'website_name' => "$website_name",
            'username' => "$user->user_login"
        ));   


        $main_body=wpautop($formated_mail_content);
        // Set content-type header to send HTML email
        $headers = array('Content-Type: text/html; charset=UTF-8');

         wp_mail($user->user_email, $sanas_mail_subject, $main_body,$headers);

    wp_send_json_success(array('message' => 'Password has been successfully reset.','siteurl' => site_url()));


    }
    add_action('wp_ajax_nopriv_sanas_reset_password_user', 'sanas_reset_password_user');
    add_action('wp_ajax_sanas_reset_password_user', 'sanas_reset_password_user');
}


if (!function_exists('sanas_save_back_canvas_data_callback_admin')) {
    function sanas_save_back_canvas_data_callback_admin() {
        // Verify the AJAX nonce for security
        check_ajax_referer('ajax-sanas-back-page-nonce', 'security');
        
        global $wpdb;
        $current_user = wp_get_current_user();

        // Get and sanitize input data
        $canvas_data = isset($_POST['canvas_data']) ? wp_slash($_POST['canvas_data']) : '';
        $card_id = isset($_POST['card_id']) ? intval($_POST['card_id']) : 0;
        $image_data = $_POST['image_data'];


        // Check if the card ID is provided
        if ($card_id && $image_data) {
            
            // Decode the base64 image data
            $image_data = str_replace('data:image/png;base64,', '', $image_data);
            $image_data = base64_decode($image_data);

            // Set the path to save the image
            $upload_dir = wp_upload_dir();
            $image_name = 'card_back_image_' . $card_id . '.png';  // Generate unique image name
            $image_path = $upload_dir['path'] . '/' . $image_name;
            $image_url = $upload_dir['url'] . '/' . $image_name;

            // Save the image to the server
            file_put_contents($image_path, $image_data);

            // Start code to update meta for canvas data and image path
            $meta = get_post_meta($card_id, 'sanas_metabox', true);
            
            if (!is_array($meta)) {
                $meta = array();
            }

            // Save the canvas data and the image URL
            $meta['sanas_upload_back_Image']['url'] = esc_url($image_url);
            $meta['sanas_back_canavs_image'] = $canvas_data;  // Adjust sanitization as necessary

            if(!empty($meta['sanas_front_canavs_image']))
            {
                $meta['sanas_front_canavs_image'] = wp_slash($meta['sanas_front_canavs_image']);  // Adjust sanitization as necessary
            }
            // Update the post meta
            update_post_meta($card_id, 'sanas_metabox', $meta);

            // Send success response with image URL
            wp_send_json_success(array(
                'message' => 'Data updated successfully',
                'card_id' => $card_id,
                'image_url' => $image_url  // Optional: return image URL
            ));

        } else {
            wp_send_json_error(array('message' => 'No data or card ID received'));
        }

        wp_die();  // Required for proper AJAX termination
    }

    // Hook into WordPress AJAX for authenticated users
    add_action('wp_ajax_sanas_save_back_canvas_data_callback_admin', 'sanas_save_back_canvas_data_callback_admin');
    
    // Hook into WordPress AJAX for non-authenticated users
    add_action('wp_ajax_nopriv_sanas_save_back_canvas_data_callback_admin', 'sanas_save_back_canvas_data_callback_admin');
}


// Sanas Save Back Page Canvas Data
if (!function_exists('sanas_save_front_canvas_data_callback_admin')) {
function sanas_save_front_canvas_data_callback_admin() {
    check_ajax_referer('ajax-sanas-fornt-page-nonce', 'security');
    global $wpdb;
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $canvas_data = isset($_POST['canvas_data']) ? wp_slash($_POST['canvas_data']) : '';
    $card_id = isset($_POST['card_id']) ? intval($_POST['card_id']) : 0;
    $image_data = $_POST['image_data'] ;


    if ($card_id) {


            // Decode the base64 image data
            $image_data = str_replace('data:image/png;base64,', '', $image_data);
            $image_data = base64_decode($image_data);

            // Set the path to save the image
            $upload_dir = wp_upload_dir();
            $image_name = 'card_cover_image_' . $card_id . '.png';  // Generate unique image name
            $image_path = $upload_dir['path'] . '/' . $image_name;
            $image_url = $upload_dir['url'] . '/' . $image_name;

            // Save the image to the server
            file_put_contents($image_path, $image_data);


        // Start code to update meta for json
        $meta = get_post_meta( $card_id, 'sanas_metabox', true );

        if( !is_array( $meta ) ) {
            $meta = array();
        }
        $meta['sanas_upload_front_Image']['url'] = esc_url($image_url);
        $meta['sanas_front_canavs_image'] = $canvas_data;

        if(!empty($meta['sanas_back_canavs_image']))
        {
            $meta['sanas_back_canavs_image'] = wp_slash($meta['sanas_back_canavs_image']);  // Adjust sanitization as necessary
        }

        update_post_meta( $card_id, 'sanas_metabox', $meta); 

        // END code to update meta for json

        wp_send_json_success(array(
            'message' => 'Data updated successfully',
            'card_id' => $card_id
        ));     

    } 
    else {
        wp_send_json_error(array('message' => 'No data or card ID received'));
    }
    wp_die();
}
add_action('wp_ajax_sanas_save_front_canvas_data_callback_admin', 'sanas_save_front_canvas_data_callback_admin'); 
add_action('wp_ajax_nopriv_sanas_save_front_canvas_data_callback_admin', 'sanas_save_front_canvas_data_callback_admin'); 
}

// Sanas Add Rsvp
// Sanas Save Fornt Page Canvas Data
if (!function_exists('sanas_save_canvas_data_callback')) {
    function sanas_save_canvas_data_callback() {
        check_ajax_referer('ajax-sanas-fornt-page-nonce', 'security');
        global $wpdb;
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $canvas_data = isset($_POST['canvas_data']) ? wp_slash($_POST['canvas_data']) : '';
        $card_id = isset($_POST['card_id']) ? intval($_POST['card_id']) : 0;
        $step_id = isset($_POST['step_id']) ? intval($_POST['step_id']) : 0;
        $image_data = $_POST['image_data'];
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
        $imageUrl = $_POST['imageUrl'];
        $colorbg = $_POST['colorbg'];
        if ($canvas_data && $card_id && $image_data) {
            // Validate base64 image data
            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $image_data)) {
                $table_name = $wpdb->prefix . 'sanas_card_event';
                if ($event_id == 0) {
                        // Insert new record
                        $result = $wpdb->insert(
                            $table_name,
                            array(
                                'event_card_id' => $card_id,
                                'event_user' => $userID,
                                'event_front_card_json_edit' => $canvas_data,    
                                'event_front_card_preview' => $image_data,
                                'event_front_bg_color' => $colorbg,
                                'event_front_bg_link' => $imageUrl,
                                'event_step_id' => $step_id,
                            ),
                            array(
                                '%d',
                                '%d',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%d'
                            )
                        );
                        if ($result === false) {
                            $error = $wpdb->last_error;
                            wp_send_json_error(array('message' => $error));
                        } else {
                            $event_id = $wpdb->insert_id; // Get the new insert ID
                            wp_send_json_success(array(
                                'message' => 'Data inserted successfully',
                                'event_id' => $event_id,
                                'card_id' => $card_id,
                                'event_front_card_json_edit' => $canvas_data
                            ));
                        }
                    }
               else {
                // Update existing record
                $result = $wpdb->update(
                    $table_name,
                    array(
                        'event_front_card_json_edit' => $canvas_data,
                        'event_front_card_preview' => $image_data,
                        'event_step_id' => $step_id,
                        'event_front_bg_link' => $imageUrl,
                        'event_front_bg_color' => $colorbg,
                    ),
                    array(
                        'event_no' => $event_id,
                        'event_card_id' => $card_id,
                        'event_user' => $userID
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s'
                    ),
                    array(
                        '%d',
                        '%d',
                        '%d'
                    )
                );
                if ($result === false) {
                    $error = $wpdb->last_error;
                    wp_send_json_error(array('message' => $error));
                } else {
                    wp_send_json_success(array(
                        'message' => 'Data updated successfully',
                        'event_id' => $event_id,
                        'card_id' => $card_id,
                        'event_front_card_json_edit' => $canvas_data
                    ));
                }
            }
            } else {
                wp_send_json_error(array('message' => 'Invalid base64 data'));
            }
        } else {
            wp_send_json_error(array('message' => 'No data or card ID received'));
        }
        wp_die();
    }
    add_action('wp_ajax_sanas_save_canvas_data_callback', 'sanas_save_canvas_data_callback');
    add_action('wp_ajax_nopriv_sanas_save_canvas_data_callback', 'sanas_save_canvas_data_callback');
}
// Sanas Save Back Page Canvas Data
if (!function_exists('sanas_save_back_canvas_data_callback')) {
function sanas_save_back_canvas_data_callback() {
    check_ajax_referer('ajax-sanas-back-page-nonce', 'security');
    global $wpdb;
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $canvas_data = isset($_POST['canvas_data']) ? wp_slash($_POST['canvas_data']) : '';
    $card_id = isset($_POST['card_id']) ? intval($_POST['card_id']) : 0;
    $step_id = isset($_POST['step_id']) ? intval($_POST['step_id']) : 0;
    $event_no = isset($_POST['event_no']) ? intval($_POST['event_no']) : 0; // Get event_no
    $image_data = $_POST['image_data'];
    $imageUrl = $_POST['imageUrl'];
    $colorbg = $_POST['colorbg'];
    if ($canvas_data && $card_id && $event_no) {
        $table_name = $wpdb->prefix . 'sanas_card_event';
        $result = $wpdb->update(
            $table_name,
            array(
                'event_card_id' => $card_id,
                'event_user' => $userID,
                'event_back_card_json_edit' => $canvas_data,
                'event_back_card_preview' => $image_data,
                'event_step_id' => $step_id,
                'event_front_bg_link' => $imageUrl,
                'event_front_bg_color' => $colorbg,
            ),
            array('event_no' => $event_no),
            array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s'
            ),
            array('%d')
        );
        if ($result === false) {
            $error = $wpdb->last_error;
            wp_send_json_error(array('message' => $error));
        } else {
            wp_send_json_success(array('message' => 'Data updated successfully'));
        }
    } 
    else {
        wp_send_json_error(array('message' => 'No data or card ID received'));
    }
    wp_die();
}
add_action('wp_ajax_sanas_save_back_canvas_data_callback', 'sanas_save_back_canvas_data_callback'); 
add_action('wp_ajax_nopriv_sanas_save_back_canvas_data_callback', 'sanas_save_back_canvas_data_callback'); 
}
// Sanas Save Rsvp Page Canvas Data
if (!function_exists('sanas_save_guest_data_callback')) {
function sanas_save_guest_data_callback() {
    check_ajax_referer('ajax-sanas-save-preview-nonce', 'security');
    global $wpdb;
    $table_name = $wpdb->prefix . 'sanas_card_event';
    // Get the posted data

    $canvas_data = isset($_POST['canvas_data']) ? wp_slash($_POST['canvas_data']) : '';    

    $event_id = intval($_POST['event_id']);
    $step_id = isset($_POST['step_id']) ? intval($_POST['step_id']) : 0;

    $result = $wpdb->update(
            $table_name,
            array(
                'event_no' => $event_id,
                'event_step_id' => $step_id,
            ),
            array('event_no' => $event_id),
            array(
                '%d',
                '%d',
            ),  
            array('%d')
        );
    if ($result !== false) {
        wp_send_json_success(array('message' => 'Preview updated successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to update RSVP ID.'));
    }
    wp_die();
}
add_action('wp_ajax_sanas_save_guest_data_callback', 'sanas_save_guest_data_callback');
add_action('wp_ajax_nopriv_sanas_save_guest_data_callback', 'sanas_save_guest_data_callback'); 
}
if (!function_exists('sanas_get_last_back_event_callback')) {
function sanas_get_last_back_event_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sanas_card_event';
    // Get the last entry from the table
    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY event_no DESC LIMIT 1"
        )
    );
    if ($result) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error(array('message' => 'No data found'));
    }
    wp_die();
}
add_action('wp_ajax_sanas_get_last_back_event_callback', 'sanas_get_last_back_event_callback'); 
add_action('wp_ajax_nopriv_sanas_get_last_back_event_callback', 'sanas_get_last_back_event_callback'); 
}
if (!function_exists('sanas_canvas_data')) {
function sanas_canvas_data() {
    // Get the canvas data
    $canvas_data = isset($_POST['canvasData']) ? sanitize_text_field($_POST['canvasData']) : '';
    if (empty($canvas_data)) {
        wp_send_json_error(array('message' => 'Canvas data is missing.'));
    }
    // Save the canvas data in the database or process it as needed
    $post_id = wp_insert_post(array(
        'post_title' => 'Canvas Data',
        'post_content' => $canvas_data,
        'post_status' => 'publish',
        'post_type' => 'canvas_data', // Use your custom post type
    ));
    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => 'Failed to save canvas data.'));
    }
    update_post_meta($post_id, 'sanas_front_canavs_image', $canvas_data);
    wp_send_json_success(array('message' => 'Canvas data saved successfully.', 'canvasData' => $canvas_data));
}
}
// 
if (!function_exists('sanas_guest_info')) {
    function sanas_guest_info() {
        global $current_user;
        wp_get_current_user();
        check_ajax_referer('ajax-sanas-save-guest-nonce', 'security');
        global $wpdb;
        $table_name = $wpdb->prefix . 'guest_details_info';

        // Get and sanitize the posted data
        $userID    =   $current_user->ID;    
        $guestName = sanitize_text_field($_POST['guestName']);
        $guestContact = sanitize_text_field($_POST['guestContact']);
        $guestEmail = sanitize_email($_POST['guestEmail']);
        $guestGroup = sanitize_text_field($_POST['guestGroup']);
        $event_id = sanitize_text_field($_POST['event_id']);
        
        // Query to check if the email exists
        $email_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE guest_event_id = %d AND guest_email = %s", 
            $event_id, $guestEmail
        ));

        if ($email_exists > 0) {
            wp_send_json_error(array('message' => 'Email already exists.'));
        } else {

        $result = $wpdb->insert(
            $table_name,
            array(
                'guest_user_id' => $userID,
                'guest_name' => $guestName,
                'guest_group' => $guestGroup,
                'guest_phone_num' => $guestContact,
                'guest_email' => $guestEmail,
                'guest_event_id' => $event_id,
            ),
            array(
                '%d', 
                '%s', 
                '%s', 
                '%s', 
                '%s', 
                '%d', 
            )
        );
        if ($result !== false) {
            // Retrieve the last inserted ID
            $guest_id = $wpdb->insert_id;
            
            // retrieve email subject and body from theme options
            // $subject = sanas_options('sanas_guest_invite_firstime_subject');
            // $body = sanas_options('sanas_guest_invite_firstime_body');

            
            // $event_id = (int) $event_id;
            // $event_table = $wpdb->prefix . 'sanas_card_event'; // Include table prefix
            // $query = $wpdb->prepare(
            //     "SELECT event_rsvp_id FROM $event_table WHERE event_no = %d",
            //     $event_id
            // );
            // $event_rsvp_id = $wpdb->get_var($query);
            // $event_data = get_post($event_rsvp_id);

            // $event_id = (int) $event_id;
            // $event_table = $wpdb->prefix . 'sanas_card_event';
            // $query = $wpdb->prepare(
            //     "SELECT event_user, event_card_id, event_rsvp_id, event_front_card_preview FROM $event_table WHERE event_no = %d",
            //     $event_id
            // );
            // $event_table_data = $wpdb->get_row($query, ARRAY_A);
            // $event_data = get_post($event_table_data['event_rsvp_id']);

            // $subject = str_replace(
            //     array('%%eventname'),
            //     array($event_data->post_name),
            //     $subject
            // );
            // $body = str_replace(
            //     array('%%guestname', '%%eventname', '%%eventdate', '%%eventtime', '%%eventlocation', '%%eventhost', '%%invitelink', '%%eventimg'),
            //     array(
            //         $guestName, 
            //         $event_data->post_name,
            //         $event_data->post_date, 
            //         $event_data->post_date, 
            //         'location', 
            //         get_the_author_meta('display_name', $event_table_data['event_user']), 
            //         $event_table_data['event_user'], 
            //         $event_table_data['event_front_card_preview']
            //     ),
            //     $body
            // );
            
            // $headers = array('Content-Type: text/html; charset=UTF-8');
            // wp_mail($guestEmail, $subject, $body, $headers);

            wp_send_json_success(array(
                'message' => 'Guest added successfully.', 
                'guest_id' => $guest_id,
                // 'event_id' => $event_id,
                // 'post_name' => $event_data->post_name,
                // 'title' => $event_data->post_title,
                // 'date' => $event_data->post_date,
                // 'img' => $event_table_data['event_front_card_preview'],
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to insert guest information.'));
        }

        }

        wp_die();
    }
    add_action('wp_ajax_sanas_guest_info', 'sanas_guest_info');
    add_action('wp_ajax_nopriv_sanas_guest_info', 'sanas_guest_info');
}
// 
if (!function_exists('sanas_guest_handle_csv_upload')) {
    function sanas_guest_handle_csv_upload() {
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'guest_details_info'; // Replace with your table name
        $fileTmpPath = $_FILES['csvFile']['tmp_name'];
        $fileName = $_FILES['csvFile']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        global $current_user;
        wp_get_current_user();
        $userID    =   $current_user->ID;    
        $event_id = sanitize_text_field($_POST['eventid']);
        if ($fileExtension === 'csv') {
            if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                // Skip the first row if it contains headers
                fgetcsv($handle);
                $rows = [];
                while (($data = fgetcsv($handle)) !== FALSE) {
                    // Sanitize data and prepare for insertion
                    $rows[] = array(
                        'guest_user_id' => $userID,
                        'guest_name'    => sanitize_text_field($data[0]),
                        'guest_email'   => sanitize_email($data[1]),
                        'guest_phone_num' => sanitize_text_field($data[2]),
                        'guest_event_id' => $event_id,
                    );
                }
                fclose($handle);
                // Bulk insert into database
                foreach ($rows as $row) {
                    $wpdb->insert(
                        $table_name,
                        $row,
                        array('%d','%s', '%s', '%s')
                    );
                }
            echo '<div class="alert alert-success">' . esc_html__('CSV data has been successfully imported.', 'sanas') . '</div>';
            } else {
              echo '<div class="alert alert-danger">' . esc_html__('Error opening the file.', 'sanas') . '</div>';   
            }
        } else {
          echo '<div class="alert alert-warning">' . esc_html__('Invalid file extension. Please upload a CSV file.', 'sanas') . '</div>';   
        }
    } else {
          echo '<div class="alert alert-danger">' . esc_html__('No file uploaded or there was an upload error.', 'sanas') . '</div>';   
    }
    wp_die(); // Required to terminate immediately and return a proper response
    }
    add_action('wp_ajax_sanas_guest_handle_csv_upload', 'sanas_guest_handle_csv_upload');
    add_action('wp_ajax_nopriv_sanas_guest_handle_csv_upload', 'sanas_guest_handle_csv_upload');
}
if (!function_exists('sanas_delete_guest_info')) {
        function sanas_delete_guest_info() {
        global $current_user, $wpdb;
        wp_get_current_user();
        $userid = $current_user->ID;
        $itemid = $_POST['itemid'];
        // Delete from guest_details_info_table
        $guest_details_info_table = $wpdb->prefix . "guest_details_info";
        $wpdb->delete($guest_details_info_table, array('guest_id' => $itemid), array('%s'));
    }
    add_action( 'wp_ajax_nopriv_sanas_delete_guest_info', 'sanas_delete_guest_info' );
    add_action( 'wp_ajax_sanas_delete_guest_info', 'sanas_delete_guest_info' );  
}
add_action('wp_ajax_nopriv_sanas_ajax_edit_guestlist_details_popup', 'sanas_ajax_edit_guestlist_details_popup');
add_action('wp_ajax_sanas_ajax_edit_guestlist_details_popup', 'sanas_ajax_edit_guestlist_details_popup');
function sanas_ajax_edit_guestlist_details_popup() {
    global $wpdb;
    if (isset($_POST['itemid'])) {
        $itemid = intval($_POST['itemid']); // Sanitize itemid
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $guest_details_info_table = $wpdb->prefix . 'guest_details_info';
        $query = $wpdb->prepare(
            "SELECT * FROM $guest_details_info_table WHERE guest_user_id = %d AND guest_id = %d",
            $userID,
            $itemid
        );
        $get_row = $wpdb->get_row($query);
        if ($get_row) {
            echo json_encode(array(
                'guestname' => esc_attr($get_row->guest_name),
                'guestphone' => esc_attr($get_row->guest_phone_num),
                'guestgroup' => esc_attr($get_row->guest_group),
                'guestemail' => wp_kses_post($get_row->guest_email),
                'guestid' => $itemid,
            ));
        } else {
            echo json_encode(array(
                'error' => 'No data found for the specified guest ID'
            ));
        }
    } else {
        echo json_encode(array(
            'error' => 'Item ID is not set'
        ));
    }
    wp_die();
}
add_action('wp_ajax_nopriv_sanas_edit_guest_info', 'sanas_edit_guest_info');
add_action('wp_ajax_sanas_edit_guest_info', 'sanas_edit_guest_info');  
function sanas_edit_guest_info() {
    global $current_user, $wpdb;
    wp_get_current_user();
    $userID = $current_user->ID;
    $allowed_html = array();
    $guestname = wp_kses($_POST['guestname'], $allowed_html);
    $guestphone = wp_kses($_POST['guestphone'], $allowed_html);
    $guestGroup = wp_kses($_POST['guestGroup'], $allowed_html);
    $guestemail = $_POST['guestemail'];
    $guestid = $_POST['guestid'];
    $guest_info_table = $wpdb->prefix . "guest_details_info"; 
    if (empty($guestname)) {
        echo '<div class="alert alert-danger" role="alert">' . esc_html__('Please Add Guest Name.', 'sanas') . '</div>';
    } else {
        $wpdb->update(
            $guest_info_table,
            array(
                'guest_name' => $guestname,
                'guest_phone_num' => $guestphone,
                'guest_email' => $guestemail,
                'guest_group' => $guestGroup
            ),
            array(
                'guest_id' => $guestid,
                'guest_user_id' => $userID
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array(
                '%d',
                '%d'
            )
        );
        echo '<div class="alert alert-success pop-btn-div" role="alert">' . esc_html__('Guest List Edit Successfully.', 'sanas') . '</div>';
    }
    die();
}


add_action('wp_ajax_nopriv_sanas_guest_invitation_response', 'sanas_guest_invitation_response');
add_action('wp_ajax_sanas_guest_invitation_response', 'sanas_guest_invitation_response');  
function sanas_guest_invitation_response() {
    global $current_user, $wpdb;
    wp_get_current_user();
    $userID = $current_user->ID;

    $allowed_html = array();
    $status = $_POST['status'];
    $prestatus = $_POST['prestatus'];
    $guestid = $_POST['guestid'];
    $kidsguest = $_POST['kidsguest'];
    $adultguest = $_POST['adultguest'];
    $prekidsguest = $_POST['prekidsguest'];
    $preadultguest = $_POST['preadultguest'];
    $mesg = $_POST['mesg'];
    $preview_url = esc_url(isset($_POST['guest_preview_url']) ? $_POST['guest_preview_url'] : '');
 
    $guest_info_table = $wpdb->prefix . "guest_details_info"; 
    $event_table = $wpdb->prefix . "sanas_card_event";

    // get guest email and event details
    $guest_email = $wpdb->get_var($wpdb->prepare("SELECT guest_email FROM $guest_info_table WHERE guest_id = %d", $guestid));
    $guest_name = $wpdb->get_var($wpdb->prepare("SELECT guest_name FROM $guest_info_table WHERE guest_id = %d", $guestid));
    $event_id = $wpdb->get_var($wpdb->prepare("SELECT guest_event_id FROM $guest_info_table WHERE guest_id = %d", $guestid));
    
    // Get event details
    $event_data = $wpdb->get_row($wpdb->prepare(
        "SELECT e.*, p.post_title as event_name, p.post_date as event_date, 
         u.display_name as host_name, p.guid as event_url
         FROM {$wpdb->prefix}sanas_card_event e
         LEFT JOIN {$wpdb->posts} p ON e.event_rsvp_id = p.ID
         LEFT JOIN {$wpdb->users} u ON e.event_user = u.ID
         WHERE e.event_no = %d",
        $event_id
    ));

    $event_image = $event_data->event_front_card_preview;
    $event_name = $event_data->event_name;
    $event_date = esc_html(get_post_meta($event_data->event_rsvp_id, 'event_date', true));
    $event_message = esc_html(get_post_meta($event_data->event_rsvp_id, 'guest_message', true));
    $program = get_post_meta($event_data->event_rsvp_id, 'listing_itinerary_details', true);
    $event_venue_name = esc_html(get_post_meta($event_data->event_rsvp_id, 'event_venue_name', true));
    $event_venue_address = esc_html(get_post_meta($event_data->event_rsvp_id, 'event_venue_address', true));
    $event_venue_address_link = esc_html(get_post_meta($event_data->event_rsvp_id, 'event_venue_address_link', true));

    $event_host = $event_data->host_name;

    // Build event timeline
    $event_time_line = '';
    if(!empty($program) && count($program)>0) {
        foreach ($program as $event) {   
            $event_time_line .= $event['program_name'].' - '.$event['program_time'].'<br>';
        }
    }

    // Build invite link with proper URL structure
    $site_url = site_url();
    $invite_link = add_query_arg(array(
        'card_id' => $event_data->event_card_id,
        'event_id' => $event_id,
        'guestid' => $guestid
    ), $site_url . '/guest-preview/');

    // Convert base64 image to URL
    if($event_image) {
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'];
        $filename = 'event-preview-' . $event_id . '.png';
        $file = $upload_path . '/' . $filename;
        
        // Remove header from base64 string
        $base64_string = str_replace('data:image/png;base64,', '', $event_image);
        $base64_string = str_replace(' ', '+', $base64_string);
        
        // Save base64 as image file
        file_put_contents($file, base64_decode($base64_string));
        
        // Get URL of saved image
        $event_image_url = $upload_dir['url'] . '/' . $filename;
    }

    $wpdb->update(
        $guest_info_table,
        array(
            'guest_status' => $status,
            'guest_kids' => $kidsguest,
            'guest_adult' => $adultguest,
            'guest_msg' => $mesg
        ),
        array('guest_id' => $guestid),
        array(
            '%s',
            '%d',
            '%d',
            '%s'
        ),
        array('%d')
    );

    if($prestatus != $status || $prekidsguest != $kidsguest || $preadultguest != $adultguest){
        echo sanas_guest_invitation_response_mail($guest_email, $status, $prestatus, $kidsguest, $prekidsguest, $adultguest, $preadultguest, $event_image_url, $guest_name, $event_name, $event_date, $event_time_line, $event_message, $invite_link, $event_host, $event_venue_name, $event_venue_address, $event_venue_address_link);
    }
    echo '<div class="alert alert-success pop-btn-div" role="alert">' . esc_html__('Guest Submitted Response Successfully.', 'sanas') . '</div>';
    echo sanas_guest_invitation_response_mail($guest_email, $status, $prestatus, $kidsguest, $prekidsguest, $adultguest, $preadultguest, $event_image_url, $guest_name, $event_name, $event_date, $event_time_line, $event_message, $invite_link, $event_host, $event_venue_name, $event_venue_address, $event_venue_address_link);
    die();
}
//send mail to guest
function sanas_guest_invitation_response_mail($guest_email, $status, $prestatus, $kidsguest, $prekidsguest, $adultguest, $preadultguest, $event_image, $guest_name, $event_name, $event_date, $event_time_line, $event_message, $invite_link, $event_host, $event_venue_name, $event_venue_address, $event_venue_address_link) {
    // Initialize subject and body
    $subject = '';
    $body = '';

    // Check if status changed or guest counts updated
    if ($status != $prestatus || $kidsguest != $prekidsguest || $adultguest != $preadultguest) {
        
        // If guest counts were updated, send update email based on current status
        if ($kidsguest != $prekidsguest || $adultguest != $preadultguest) {
            switch($status) {
                case 'May Be':
                    $subject = sanas_options('sanas_guest_update_maybe_subject');
                    $body = sanas_options('sanas_guest_update_maybe_body');
                    break;
                case 'Accepted': 
                    $subject = sanas_options('sanas_guest_update_yes_subject');
                    $body = sanas_options('sanas_guest_update_yes_body');
                    break;
                case 'Declined':
                    $subject = sanas_options('sanas_guest_update_no_subject');
                    $body = sanas_options('sanas_guest_update_no_body');
                    break;
            }
        }
        // If only status changed, send status change email
        else if ($status != $prestatus) {
            switch($status) {
                case 'Declined':
                    $subject = sanas_options('sanas_guest_declined_subject');
                    $body = sanas_options('sanas_guest_declined_body');
                    break;
                case 'May Be':
                    $subject = sanas_options('sanas_guest_maybe_subject');
                    $body = sanas_options('sanas_guest_maybe_body');
                    break;
                case 'Accepted':
                    $subject = sanas_options('sanas_guest_yes_subject');
                    $body = sanas_options('sanas_guest_yes_body');
                    break;
            }
        }

        // Replace placeholders in email content
        $body = str_replace(
            array('%%guestname', '%%gueststatus', '%%guestkids', '%%guestadult', '%%eventimg', '%%eventname', '%%eventdate', '%%eventtime', '%%eventmessage', '%%invitelink', '%%eventhost', '%%eventvenue', '%%venueaddress', '%%googlelink'), 
            array($guest_name, $status, $kidsguest, $adultguest, $event_image, $event_name, $event_date, $event_time_line, $event_message, $invite_link, $event_host, $event_venue_name, $event_venue_address, $event_venue_address_link),
            $body
        );

        $subject = str_replace(
            array('%%guestname', '%%eventname'), 
            array($guest_name, $event_name),
            $subject
        );

        // Send email
        // $headers = array('Content-Type: text/html; charset=UTF-8');
        
        try {
            $akey = file_get_contents('config.txt');
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $akey);
            $apiInstance = new TransactionalEmailsApi(new GuzzleHttp\Client(), $config);
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => $subject,
                'sender' => ['name' => 'Stexas', 'email' => 'stexas132@gmail.com'],
                'to' => [['email' => $guest_email, 'name' => $guest_name]],
                'htmlContent' => $body
            ]);
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            print_r($result);
            echo 'Email sent successfully!';
        } catch (Exception $e) {
            echo 'Error sending email: ', $e->getMessage(), PHP_EOL;
        }
        // wp_mail($guest_email, $subject, $body, $headers);
    }else{
        echo "Nothing changed";
    }
}

// send mail to host auto
function sanas_guest_invitation_response_mail_10min_before($event_id) {
    global $wpdb;

    // Get event details
    $sanas_card_event_table = $wpdb->prefix . 'sanas_card_event';
    $event_details = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT event_name, event_date, event_time_line, event_location, event_host FROM $sanas_card_event_table WHERE event_no = %d",
            $event_id
        )
    );

    if ($event_details) {
        $event_name = $event_details->event_name;
        $event_date = $event_details->event_date;
        $event_time_line = $event_details->event_time_line;
        $event_location = $event_details->event_location;
        $event_host = $event_details->event_host;

        // Get all accepted guests for the event
        $guest_info_table = $wpdb->prefix . "guest_details_info";
        $accepted_guests = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT guest_email, guest_name FROM $guest_info_table WHERE guest_event_id = %d AND guest_status = 'Accepted'",
                $event_id
            )
        );

        // Calculate the time to send the email
        $event_date_timestamp = strtotime($event_date . ' ' . $event_time_line);
        $ten_minutes_before_event = date('Y-m-d H:i:s', strtotime('-10 minutes', $event_date_timestamp));

        // Check if the current time is 10 minutes before the event
        if (current_time('Y-m-d H:i:s') >= $ten_minutes_before_event && current_time('Y-m-d H:i:s') < $event_date_timestamp) {
            foreach ($accepted_guests as $guest) {
                $guest_email = $guest->guest_email;
                $guest_name = $guest->guest_name;

                $subject = sanas_options('sanas_guest_10min_before_subject');
                $body = sanas_options('sanas_guest_10min_before_body');

                $body = str_replace(
                    array('%%guestname', '%%eventname', '%%eventdate', '%%eventtime', '%%eventlocation', '%%eventhost'),
                    array($guest_name, $event_name, $event_date, $event_time_line, $event_location, $event_host),
                    $body
                );

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($guest_email, $subject, $body, $headers);
            }
        }
    }
}

// Schedule the cron job if not already scheduled
if (!wp_next_scheduled('sanas_guest_invitation_response_mail_10min_before_cron')) {
    wp_schedule_event(time(), 'every_minute', 'sanas_guest_invitation_response_mail_10min_before_cron');
}

// Hook the event to the function
add_action('sanas_guest_invitation_response_mail_10min_before_cron', function() {
    global $wpdb;
    $event_ids = $wpdb->get_col("SELECT DISTINCT guest_event_id FROM {$wpdb->prefix}guest_details_info WHERE guest_status = 'Accepted'");
    foreach ($event_ids as $event_id) {
        sanas_guest_invitation_response_mail_10min_before($event_id);
    }
});

function deactivate_event_email_10min_before_cron() {
    $timestamp = wp_next_scheduled('sanas_guest_invitation_response_mail_10min_before_cron');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'sanas_guest_invitation_response_mail_10min_before_cron');
    }
}
register_deactivation_hook(__FILE__, 'deactivate_event_email_10min_before_cron');



add_action('wp_ajax_nopriv_sanas_open_guest_invitation_response', 'sanas_open_guest_invitation_response');
add_action('wp_ajax_sanas_open_guest_invitation_response', 'sanas_open_guest_invitation_response');  
function sanas_open_guest_invitation_response() {
    global $current_user, $wpdb;
    wp_get_current_user();
    $userID = $current_user->ID;

    $allowed_html = array();
    $status = 'Accepted';
    $event_id = $_POST['event_id'];
    $event_userid = $_POST['event_userid'];
    $name = $_POST['name'];
    $email = sanitize_email($_POST['email']);  // Sanitize the email input
    $phone = sanitize_text_field($_POST['phone']);  // Sanitize the phone input

    $kidsguest = $_POST['kidsguest'];
    $adultguest = $_POST['adultguest'];
    $guest_preview_url = esc_url(isset($_POST['guest_preview_url']) ? $_POST['guest_preview_url'] : '');
    $mesg = $_POST['mesg'];
 
    $guest_info_table = $wpdb->prefix . "guest_details_info"; 

    // Check if email already exists for the specific user and event
    $email_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $guest_info_table WHERE guest_email = %s AND guest_user_id = %d AND guest_event_id = %s",
            $email,
            $event_userid,
            $event_id
        )
    );

    // Check if phone number already exists for the specific user and event
    $phone_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $guest_info_table WHERE guest_phone_num = %s AND guest_user_id = %d AND guest_event_id = %s",
            $phone,
            $event_userid,
            $event_id
        )
    );

    if ($email_exists > 0 && !empty($email)) {
        wp_send_json_error(array('message' => 'Email already exists.'));
    } elseif ($phone_exists > 0 && !empty($phone)) {
        wp_send_json_error(array('message' => 'Phone already exists.'));
    } 
    else{
            $wpdb->insert(
            $guest_info_table,
            array(
                'guest_status' => $status,
                'guest_kids' => $kidsguest,
                'guest_adult' => $adultguest,
                'guest_event_id' => $event_id,
                'guest_msg' => $mesg,
                'guest_name' => $name,
                'guest_email' => $email,
                'guest_phone_num' => $phone,
                'guest_user_id' => $event_userid // include this if needed as part of the insert fields
            ),
            array(
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d' // include this if guest_id needs specific formatting
            )
        );  
        
    }




        if ($result !== false) {
            // Retrieve the last inserted ID
            $insert_id = $wpdb->insert_id;
            if ($insert_id) {
                wp_send_json_success(array(
                    'message' => 'Guest Inserted Successfully.',
                    'guest_id' => $insert_id,
                    'url' => $guest_preview_url.'&guestid='.$insert_id // Include the guest ID in the response
                ));
            }
            else{
                wp_send_json_error(array('message' => 'Failed to insert guest information.'));
            }
        } else {
            wp_send_json_error(array('message' => 'Failed to insert guest information.'));
        }

    echo '<div class="alert alert-success pop-btn-div" role="alert">' . esc_html__('Guest Inserted Successfully.', 'sanas') . '</div>';

    die();
}


add_action('wp_ajax_nopriv_update_guest_groups', 'update_guest_groups');
add_action('wp_ajax_update_guest_groups', 'update_guest_groups');
function update_guest_groups() {
    check_ajax_referer('ajax-user-guestlist-group-nonce', 'security');
    global $current_user, $wpdb;
    wp_get_current_user();
    $userID = $current_user->ID;
    $event_id = $_POST['event_id'];
    $table_name = $wpdb->prefix . 'guest_list_group';
    // Retrieve existing groups for the user
    $existingList = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT guest_group_name FROM $table_name WHERE guest_group_user = %d",
            $userID
        )
    );
    // Get new groups from AJAX request
    $groupNames = isset($_POST['groupNames']) ? array_map('sanitize_text_field', $_POST['groupNames']) : [];
    $deletedGroups = isset($_POST['deletedGroups']) ? array_map('sanitize_text_field', $_POST['deletedGroups']) : [];
    // Ensure both arrays are not empty
    if (empty($groupNames) && empty($deletedGroups)) {
        wp_send_json_error('No groups to update.');
        wp_die();
    }
    // Find new groups not already in the existing list
    $newGroups = array_diff($groupNames, $existingList);
    // Insert new groups into the database
    foreach ($newGroups as $group) {

        if(!empty($group))
        {
            $wpdb->insert(
                $table_name,
                array(
                    'guest_group_user' => $userID,
                    'guest_group_name' => $group,
                    'guest_event_id' => $event_id,
                ),
                array('%d', '%s','%d')
            );            
        }
    }
    // Remove deleted groups from the database
    foreach ($deletedGroups as $group) {
        $wpdb->delete(
            $table_name,
            array(
                'guest_group_user' => $userID,
                'guest_group_name' => $group
            ),
            array('%d', '%s')
        );
    }
    echo '<div class="alert alert-success pop-btn-div" role="alert">
       ' . esc_html__('Guest Groups Updated Successfully.', 'sanas') . '</div>';
    wp_die();
}
if (!function_exists('sanas_save_rsvp_data_callback')) {
    function sanas_save_rsvp_data_callback() {
        check_ajax_referer('ajax-sanas-save-rsvp-nonce', 'security');
        global $wpdb;
        $guest_name = sanitize_text_field($_POST['guestName']);
        $guest_contact = sanitize_text_field($_POST['guestContact']);
        $guest_message = wp_kses_post($_POST['guestMessage']);
        $event_title = wp_kses_post($_POST['eventTitle']);
        $event_date = wp_kses_post($_POST['eventDate']);
        $itinerary = wp_kses_post($_POST['itinerary']);    
        $video_src = esc_url($_POST['videoSrc']);
        $youtube_src = esc_url($_POST['youtubeSrc']);
        $itinerary_data = json_decode(stripslashes($_POST['itineraryData']), true); // Decode JSON string
        $registry_data = json_decode(stripslashes($_POST['registryData']), true); // Decode JSON string
        $rsvp_id = isset($_POST['rsvp_id']) ? intval($_POST['rsvp_id']) : 0;
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
        $venue_name = $_POST['event_venue_name'];
        $venue_address = $_POST['event_venue_address'];
        $venue_address_link = $_POST['event_venue_address_link'];

        $eventtitlecss = $_POST['eventtitlecss'];
        $guestNamecss  = $_POST['guestNamecss'];
        $guestContactcss = $_POST['guestContactcss'];
        $guestMessagecss = $_POST['guestMessagecss'];
        $eventdatecss = $_POST['eventdatecss'];
        $itinerarycss = $_POST['itinerarycss'];



        $rsvp_bg_url = $_POST['rsvp_bg_url'];
        if ($guest_name === '') {
            alert('Guest Name is required fields.');
            return; // Stop execution if validation fails
        }
        elseif($guest_contact === ''){
            alert('Guest Contact is required fields.');
            return; // Stop execution if validation fails
        }
        if ($rsvp_id == 0) {
            // Create a new RSVP post
            $new_rsvp = array(
                'post_title' => $guest_name, 
                'post_type' => 'sanas_rsvp',
                'post_status' => 'publish'
            );
            $rsvp_id = wp_insert_post($new_rsvp);
            if ($rsvp_id) {
                update_post_meta($rsvp_id, 'guest_name', $guest_name);
                update_post_meta($rsvp_id, 'guest_contact', $guest_contact);
                update_post_meta($rsvp_id, 'guest_message', $guest_message);
                update_post_meta($rsvp_id, 'event_name', $event_title);
                update_post_meta($rsvp_id, 'event_date', $event_date);
                update_post_meta($rsvp_id, 'itinerary', $itinerary);

                update_post_meta($rsvp_id, 'guest_name_css', $guestNamecss);
                update_post_meta($rsvp_id, 'guest_contact_css', $guestContactcss);
                update_post_meta($rsvp_id, 'guest_message_css', $guestMessagecss);
                update_post_meta($rsvp_id, 'event_title_css', $eventtitlecss);
                update_post_meta($rsvp_id, 'event_date_css', $eventdatecss);
                update_post_meta($rsvp_id, 'itinerarycss', $itinerarycss);
                update_post_meta($rsvp_id, 'event_venue_name', $venue_name);
                update_post_meta($rsvp_id, 'event_venue_address', $venue_address);
                update_post_meta($rsvp_id, 'event_venue_address_link', $venue_address_link);

                if (!empty($video_src)) {
                    update_post_meta($rsvp_id, 'opt_upload_video', $video_src);
                }
                if (!empty($youtube_src)) {
                    update_post_meta($rsvp_id, 'opt_upload_video', $youtube_src); // Store YouTube URL separately
                }
                if (!empty($itinerary_data)) {
                    update_post_meta($rsvp_id, 'listing_itinerary_details', $itinerary_data);
                }
                else{
                    update_post_meta($rsvp_id, 'listing_itinerary_details', '');
                }
                if (!empty($registry_data)) {
                    update_post_meta($rsvp_id, 'registries', $registry_data);
                }else{
                    update_post_meta($rsvp_id, 'registries', '');
                }
                // Insert the rsvp_id and step_no into the _sanas_card_event table
                $event_no = $event_id; // Your specific event number
                $step_no = 3; // Your specific step number
                $result = $wpdb->update(
                    $wpdb->prefix . 'sanas_card_event',
                    array(
                        'event_rsvp_id' => $rsvp_id,
                        'event_step_id' => $step_no,
                        'event_rsvp_bg_link' => $rsvp_bg_url,  // Set the event_step_id here
                    ),
                    array('event_no' => $event_no), // Update row where event_no matches
                    array(
                        '%d', // Format for event_rsvp_id
                        '%d',  // Format for event_step_id
                        '%s',  // Format for event_rsvp_bg_link
                    ),
                    array('%d') // Format for event_no
                );
                if ($result !== false) {
                    echo json_encode(array('status' => 'success', 'message' => 'RSVP saved successfully!', 'post_id' => $rsvp_id));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Failed to update the event details.'));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed to save RSVP.'));
            }
        } else {
            // Update existing RSVP post
             $event_no = $event_id; // Your specific event number
                $result = $wpdb->update(
                    $wpdb->prefix . 'sanas_card_event',
                    array(
                        'event_rsvp_id' => $rsvp_id,
                        'event_rsvp_bg_link' => $rsvp_bg_url, // Set the event_step_id here
                    ),
                    array('event_no' => $event_no), // Update row where event_no matches
                    array(
                        '%d', // Format for event_rsvp_id
                        '%s',  // Format for event_rsvp_bg_link  
                    ),
                    array('%d') // Format for event_no
                );
            $update_rsvp = array(
                'ID' => $rsvp_id,
                'post_title' => $guest_name,
            );
            $updated = wp_update_post($update_rsvp);
            if ($updated) {
                update_post_meta($rsvp_id, 'guest_name', $guest_name);
                update_post_meta($rsvp_id, 'guest_contact', $guest_contact);
                update_post_meta($rsvp_id, 'guest_message', $guest_message);
                update_post_meta($rsvp_id, 'event_name', $event_title);
                update_post_meta($rsvp_id, 'event_date', $event_date);
                update_post_meta($rsvp_id, 'itinerary', $itinerary);

                update_post_meta($rsvp_id, 'guest_name_css', $guestNamecss);
                update_post_meta($rsvp_id, 'guest_contact_css', $guestContactcss);
                update_post_meta($rsvp_id, 'guest_message_css', $guestMessagecss);
                update_post_meta($rsvp_id, 'event_title_css', $eventtitlecss);
                update_post_meta($rsvp_id, 'event_date_css', $eventdatecss);
                update_post_meta($rsvp_id, 'itinerarycss', $itinerarycss);
                update_post_meta($rsvp_id, 'event_venue_name', $venue_name);
                update_post_meta($rsvp_id, 'event_venue_address', $venue_address);
                update_post_meta($rsvp_id, 'event_venue_address_link', $venue_address_link);

                if (!empty($video_src)) {
                    update_post_meta($rsvp_id, 'opt_upload_video', $video_src);
                }
                if (!empty($youtube_src)) {
                    update_post_meta($rsvp_id, 'opt_upload_video', $youtube_src); // Store YouTube URL separately
                }
                if (!empty($itinerary_data)) {
                    update_post_meta($rsvp_id, 'listing_itinerary_details', $itinerary_data);
                }
                else{
                    update_post_meta($rsvp_id, 'listing_itinerary_details', '');
                }
                if (!empty($registry_data)) {
                    update_post_meta($rsvp_id, 'registries', $registry_data);
                }
                else{
                    update_post_meta($rsvp_id, 'registries', '');
                }
                echo json_encode(array('status' => 'success', 'message' => 'RSVP updated successfully!', 'post_id' => $rsvp_id));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed to update RSVP.'));
            }
        }
        wp_die();
    }
    add_action('wp_ajax_sanas_save_rsvp_data_callback', 'sanas_save_rsvp_data_callback');
    add_action('wp_ajax_nopriv_sanas_save_rsvp_data_callback', 'sanas_save_rsvp_data_callback');
}
add_action('wp_ajax_sanas_send_invitations', 'sanas_send_invitations');
add_action('wp_ajax_nopriv_sanas_send_invitations', 'sanas_send_invitations');

function sanas_send_invitations() {
     global $wpdb,$current_user;
    wp_get_current_user();
    $userID = $current_user->ID;     
    if (!isset($_POST['emails'])) {
        wp_send_json_error('No emails provided.');
        return;
    }

    $sanas_card_event_table = $wpdb->prefix . 'sanas_card_event';
    $guest_details_info_table = $wpdb->prefix . "guest_details_info";
    
    $preview_url = esc_url($_POST['preview_url']);
    $preview_image = $_POST['preview_image'];
    $mailtitle = $_POST['mailtitle'];
    $event_id = $_POST['event_id'];
    $emails = json_decode(stripslashes($_POST['emails']), true);


     $emails = isset($_POST['emails']) ? json_decode(stripslashes($_POST['emails']), true) : [];
     $guestids = isset($_POST['guestids']) ? json_decode(stripslashes($_POST['guestids']), true) : [];


    $sanas_guest_invite_email_subject  = sanas_options('sanas_guest_invite_email_subject');
    $sanas_guest_invite_email_body  = sanas_options('sanas_guest_invite_email_body');



    $website_url = site_url();
    $website_name = get_option('blogname');

    $frontimagequery = $wpdb->prepare(
            "SELECT event_front_card_preview FROM $sanas_card_event_table WHERE event_no = %d",
            $event_id
        );
    $preview_card_url = $wpdb->get_var($frontimagequery);
    

    

    if (!empty($emails) && !empty($guestids) && count($emails) === count($guestids)) {

    foreach ($emails as $index => $email) {
                
        $guestId = $guestids[$index];
        $guestemail = $emails[$index];


    $guest_url=$preview_url.'&guestid='.$guestId;

    $get_guest_name =     $wpdb->prepare(
        "SELECT guest_name FROM $guest_details_info_table WHERE guest_user_id = %d AND guest_id = %d ORDER BY guest_name ASC",
        $userID,
        $guestId
    );

    $guest_name = $wpdb->get_var($get_guest_name);


    $get_guest_status =     $wpdb->prepare(
        "SELECT guest_status FROM $guest_details_info_table WHERE guest_user_id = %d AND guest_id = %d",
        $userID,
        $guestId
    );

    $guest_status = $wpdb->get_var($get_guest_status);


    $get_rsvp =   $wpdb->prepare(
            "SELECT event_rsvp_id FROM $sanas_card_event_table WHERE event_no = %d",
            $event_id
        );

    $rsvp_id = $wpdb->get_var($get_rsvp);

    $guestName = esc_html(get_post_meta($rsvp_id, 'guest_name', true));
    $eventtitle = esc_html(get_post_meta($rsvp_id, 'event_name', true));
    $eventdate = esc_html(get_post_meta($rsvp_id, 'event_date', true));
    $guestContact = esc_html(get_post_meta($rsvp_id, 'guest_contact', true));
    $guestMessage = esc_html(get_post_meta($rsvp_id, 'guest_message', true));
    $program = get_post_meta($rsvp_id, 'listing_itinerary_details', true);
    $event_venue_name = esc_html(get_post_meta($rsvp_id, 'event_venue_name', true));
    $event_venue_address = esc_html(get_post_meta($rsvp_id, 'event_venue_address', true));
    $event_venue_address_link = esc_html(get_post_meta($rsvp_id, 'event_venue_address_link', true));


    $formated_subject = sanas_sprintf("$sanas_guest_invite_email_subject", array(
        'website_url' => "$website_url",
        'website_name' => "$website_name",
        'guest_id' => "$guestId",
        'guest_name' => "$guest_name",
        'guest_url' => "$guest_url",
        'invite_title' => "$mailtitle",
        'subject_title' => "$mailtitle"
    ));

        $event_time_line='';
        if(!empty($program) && count($program)>0)
        {
            foreach ($program as $event) 
            {   
                $event_time_line.=$event['program_name'].' - '.$event['program_time'].'<br>';
            }
        }


                $sanas_mail_subject  = sanas_options('sanas_guest_invite_firstime_subject');
                $sanas_mail_body  = sanas_options('sanas_guest_invite_firstime_body');

                $website_url = site_url();
                $website_name = get_option('blogname');


                $formated_mail_subject = sanas_sprintf("$sanas_mail_subject", array(
                    'website_url' => "$website_url",
                    'website_name' => "$website_name",
                    'eventname' => "$mailtitle",
                    'eventhost' => "$guestName",
                ));   
                $formated_mail_body = sanas_sprintf("$sanas_mail_body", array(
                    'website_url' => "$website_url",
                    'website_name' => "$website_name",
                    'guestname' => "$guest_name",                    
                    'eventimg' => "$preview_image",
                    'eventdate' => "$eventdate",
                    'eventtime' => $event_time_line,
                    'eventmessage' => "$guestMessage",
                    'invitelink' => "$guest_url",
                    'eventname' => "$mailtitle",
                    'eventhost' => "$guestName",
                    'guestemail' => "$guestemail",
                    'eventvenue' => "$event_venue_name",
                    'venueaddress' => "$event_venue_address",
                    'googlelink' => "$event_venue_address_link"
                ));   


                $headers = array('Content-Type: text/html; charset=UTF-8');
                $new_formated_mail = '<style>
    @import url("https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap");</style>' . $formated_mail_body;
                // Send the email using
                wp_mail($guestemail, $formated_mail_subject, $new_formated_mail, $headers);

                // Update the database for this guestId
                $guest_details_info = $wpdb->prefix . 'guest_details_info'; // Replace with your table name

                if(empty($guest_status))
                {
                    $result = $wpdb->update(
                       $guest_details_info,
                        array('guest_status' => 'pending'),
                        array('guest_id' => $guestId), // Update row where event_no matches
                        array('%s'),
                        array('%d') // Format for event_no
                    );                    
                }

               
            }
             wp_send_json_success('Emails sent successfully.');
                                 
        }       

}
add_action('wp_ajax_sanas_backend_upload_image', 'sanas_backend_upload_image');
add_action('wp_ajax_sanas_backend_upload_image', 'sanas_backend_upload_image');

function sanas_backend_upload_image() {
    if (isset($_FILES['image']) && isset($_POST['user_id'])) {
        $file = $_FILES['image'];
        $user_id = intval($_POST['user_id']);
        
        // Check for file upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => 'File upload error.']);
            return;
        }
        
        // Handle file upload
        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (isset($upload['file'])) {
            $file_name = basename($upload['file']);
            $attachment = [
                'post_mime_type' => $upload['type'],
                'post_title' => sanitize_file_name($file_name),
                'post_content' => '',
                'post_status' => 'inherit',
                'post_author' => $user_id // Set the user ID
            ];

            $attachment_id = wp_insert_attachment($attachment, $upload['file']);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
            wp_update_attachment_metadata($attachment_id, $attach_data);

            $image_src = wp_get_attachment_image_src($attachment_id, 'full');
            $image_url = $image_src ? $image_src[0] : '';

            wp_send_json_success(['image_url' => $image_url]);

        } else {
            wp_send_json_error(['message' => 'Failed to handle file upload.']);
        }
    } else {
        wp_send_json_error(['message' => 'Invalid request.']);
    }
}


if ( !function_exists('sanas_sprintf' ) ){
function sanas_sprintf($str='', $vars=array(), $char='%%'){
    if (!$str) return '';
    if (count($vars) > 0)
    {
        foreach ($vars as $k => $v)
        {
            $str = str_replace($char . $k, $v, $str);
        }
    }

    return $str;
    }
}


// AJAX handler for loading images based on category selection
add_action('wp_ajax_sanas_load_category_images', 'sanas_load_category_images');
add_action('wp_ajax_nopriv_sanas_load_category_images', 'sanas_load_category_images');

function sanas_load_category_images() {
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    

    $html ='';


    $device_id = $_POST['device_id'];   
    if ($category_id) {
        if($category_id=='all'){
            $card_category_front_gallery = get_term_meta($term->term_id, 'card_category_front_gallery', true);

            $gallery_ids = explode(',', $card_category_front_gallery);
            $all_gallery_ids = array_merge($all_gallery_ids, array_filter($gallery_ids));

            $gallery_ids=$all_gallery_ids;
        }
        else{
            $gallery_ids = get_term_meta($category_id, 'card_category_front_gallery', true);
            $gallery_ids = explode(',', $gallery_ids);

        }
    }
    else{

           $terms = get_terms(array(
                'taxonomy'   => 'sanas-card-category',
                'hide_empty' => false,  // Set to true if you want to hide terms without posts
            ));

            $gallery_ids = [];  // Array to store all image IDs across terms

            // Gather all gallery image IDs across terms
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                      $card_category_front_gallery = get_term_meta($term->term_id, 'card_category_front_gallery', true);
                    $gallery_ids_new = explode(',', $card_category_front_gallery);
                    $gallery_ids = array_merge($gallery_ids, array_filter($gallery_ids_new));
                }
            }

    }    



         if ($device_id == 'mobile') {
            foreach ($gallery_ids as $gallery_item_id) {
                $html .= '<div class="tamplate-iteam">
                            <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                          </div>';
            }

         }else{   
             $i = 0;
            $html = '<div class="tamplate-inner">';
            foreach ($gallery_ids as $gallery_item_id) {

              if ($i % 2 == 0 && $i !== 0) {
                  $html .= '</div><div class="tamplate-inner">';
              }

                $html .= '<div class="tamplate-iteam">
                            <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                          </div>';

                $i++;          
            }

            $html .= '</div>';
        }
    

        echo $html;
   

    wp_die();
}


// AJAX handler for loading images based on category selection
add_action('wp_ajax_sanas_load_category_images_back', 'sanas_load_category_images_back');
add_action('wp_ajax_nopriv_sanas_load_category_images', 'sanas_load_category_images_back');

function sanas_load_category_images_back() {
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    $html ='';
    $device_id = $_POST['device_id'];   
    if ($category_id) {
        $gallery_ids = get_term_meta($category_id, 'card_category_back_gallery', true);
        $gallery_ids = explode(',', $gallery_ids);

         if ($device_id == 'mobile') {
            foreach ($gallery_ids as $gallery_item_id) {
                $html .= '<div class="tamplate-iteam">
                            <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                          </div>';
            }

         }else{   
             $i = 0;
            $html = '<div class="tamplate-inner">';
            foreach ($gallery_ids as $gallery_item_id) {

              if ($i % 2 == 0 && $i !== 0) {
                  $html .= '</div><div class="tamplate-inner">';
              }

                $html .= '<div class="tamplate-iteam">
                            <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                          </div>';

                $i++;          
            }

            $html .= '</div>';
        }
    

        echo $html;
    }

    wp_die();
}


// AJAX handler for gallery caption search
add_action('wp_ajax_sanas_search_gallery_by_caption', 'sanas_search_gallery_by_caption');
add_action('wp_ajax_nopriv_sanas_search_gallery_by_caption', 'sanas_search_gallery_by_caption');

function sanas_search_gallery_by_caption() {
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';

    // Query categories in the taxonomy
    $terms = get_terms(array(
        'taxonomy'   => 'sanas-card-category',
        'hide_empty' => false,
    ));

    if ($device_id != 'mobile') {
        $html = '<div class="template-inner">';
    }
    foreach ($terms as $term) {
        // Get gallery images for each term
        $gallery_ids = get_term_meta($term->term_id, 'card_category_front_gallery', true);
        $gallery_ids = explode(',', $gallery_ids);

        if (!empty($gallery_ids)) {
            $i=0;
            foreach ($gallery_ids as $gallery_item_id) {
                // Get caption and check if it matches the search term
                $caption = wp_get_attachment_caption($gallery_item_id);
                if (stripos($caption, $search_term) !== false) { // Case-insensitive search

                     if ($device_id == 'mobile') {
                        $html .= '<div class="tamplate-iteam">
                                <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                              </div>';
                     }
                     else{

                    if ($i % 2 == 0 && $i !== 0) {
                    $html .= '</div><div class="tamplate-inner">';
                    }
                    $image_url = wp_get_attachment_url($gallery_item_id);
                    $html .= '<div class="tamplate-iteam">
                            <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                          </div>';

                     $i++;       

                     }   

 
                }
            }
        }
    }
    if ($device_id != 'mobile') {

        $html .= '</div>';
    }
    echo $html;
    wp_die();
}
// AJAX handler for gallery caption search
add_action('wp_ajax_sanas_search_gallery_by_caption_back', 'sanas_search_gallery_by_caption_back');
add_action('wp_ajax_nopriv_sanas_search_gallery_by_caption_back', 'sanas_search_gallery_by_caption_back');

function sanas_search_gallery_by_caption_back() {
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';

    // Query categories in the taxonomy
    $terms = get_terms(array(
        'taxonomy'   => 'sanas-card-category',
        'hide_empty' => false,
    ));

    if ($device_id != 'mobile') {
        $html = '<div class="template-inner">';
    }
    foreach ($terms as $term) {
        // Get gallery images for each term
        $gallery_ids = get_term_meta($term->term_id, 'card_category_back_gallery', true);
        $gallery_ids = explode(',', $gallery_ids);

        if (!empty($gallery_ids)) {
            $i=0;
            foreach ($gallery_ids as $gallery_item_id) {
                // Get caption and check if it matches the search term
                $caption = wp_get_attachment_caption($gallery_item_id);
                if (stripos($caption, $search_term) !== false) { // Case-insensitive search

                     if ($device_id == 'mobile') {
                        $html .= '<div class="tamplate-iteam">
                                <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                              </div>';
                     }
                     else{

                    if ($i % 2 == 0 && $i !== 0) {
                    $html .= '</div><div class="tamplate-inner">';
                    }
                    $image_url = wp_get_attachment_url($gallery_item_id);
                    $html .= '<div class="tamplate-iteam">
                            <img src="' . wp_get_attachment_url($gallery_item_id) . '" alt="">
                          </div>';

                     $i++;       

                     }   

 
                }
            }
        }
    }
    if ($device_id != 'mobile') {

        $html .= '</div>';
    }
    echo $html;
    wp_die();
}


add_action('wp_ajax_sanas_load_fabric_js_data_front', 'sanas_load_fabric_js_data_front');
add_action('wp_ajax_nopriv_sanas_load_fabric_js_data_front', 'sanas_load_fabric_js_data_front');
// AJAX handler to load canvas JSON data from post meta
function sanas_load_fabric_js_data_front() {
    if (isset($_POST['card_id'])) {
        $card_id = intval($_POST['card_id']);

        $meta = get_post_meta( $card_id, 'sanas_metabox', true );

        if( !is_array( $meta ) ) {
            $meta = array();
        }

        $json_data = isset($meta['sanas_front_canavs_image']) ? stripslashes(htmlspecialchars_decode($meta['sanas_front_canavs_image'])) : '';
        wp_send_json_success(['json_data' => $json_data]);
    } else {
        wp_send_json_error('Invalid post ID.');
    }
}


add_action('wp_ajax_sanas_load_fabric_js_data_back', 'sanas_load_fabric_js_data_back');
add_action('wp_ajax_nopriv_sanas_load_fabric_js_data_back', 'sanas_load_fabric_js_data_back');
// AJAX handler to load canvas JSON data from post meta
function sanas_load_fabric_js_data_back() {
    if (isset($_POST['card_id'])) {
        $card_id = intval($_POST['card_id']);

        $meta = get_post_meta( $card_id, 'sanas_metabox', true );

        if( !is_array( $meta ) ) {
            $meta = array();
        }

        $json_data = isset($meta['sanas_back_canavs_image']) ? stripslashes(htmlspecialchars_decode($meta['sanas_back_canavs_image'])) : ''; 
        wp_send_json_success(['json_data' => $json_data]);
    } else {
        wp_send_json_error('Invalid post ID.');
    }
}



add_action('wp_ajax_sanas_load_fabric_js_data_front_user', 'sanas_load_fabric_js_data_front_user');
add_action('wp_ajax_nopriv_sanas_load_fabric_js_data_front_user', 'sanas_load_fabric_js_data_front_user');
// AJAX handler to load canvas JSON data from post meta
function sanas_load_fabric_js_data_front_user() {
    if (isset($_POST['card_id'])) {

         global $wpdb;

        $card_id = intval($_POST['card_id']);
        $event_id = intval($_POST['event_id']);


        $card_post = get_post($card_id);
        $sanas_card_event_table = $wpdb->prefix . 'sanas_card_event';
        $frontpagequery = $wpdb->prepare(
            "SELECT event_front_card_json_edit 
             FROM $sanas_card_event_table 
             WHERE event_card_id = %d 
               AND event_no = %d",
            $card_id,
            $event_id
        );
        // Execute the query and get the result
        $frontpagedata = $wpdb->get_var($frontpagequery);
        $sanas_portfolio_meta = get_post_meta($card_id,'sanas_metabox',true);
        $frontmetadata=$sanas_portfolio_meta['sanas_front_canavs_image'];
        if (!empty($frontpagedata)) {
            $data = stripslashes(stripslashes(htmlspecialchars_decode($frontpagedata)));
        } 
        else {
            $data = stripslashes(htmlspecialchars_decode($frontmetadata));
        }


        $json_data = $data;
        wp_send_json_success(['json_data' => $json_data]);
    } else {
        wp_send_json_error('Invalid post ID.');
    }
}



add_action('wp_ajax_sanas_load_fabric_js_data_back_user', 'sanas_load_fabric_js_data_back_user');
add_action('wp_ajax_nopriv_sanas_load_fabric_js_data_back_user', 'sanas_load_fabric_js_data_back_user');
// AJAX handler to load canvas JSON data from post meta
function sanas_load_fabric_js_data_back_user() {
    if (isset($_POST['card_id'])) {

         global $wpdb;

        $card_id = intval($_POST['card_id']);
        $event_id = intval($_POST['event_id']);


        $card_post = get_post($card_id);
        $sanas_card_event_table = $wpdb->prefix . 'sanas_card_event';
        $frontpagequery = $wpdb->prepare(
            "SELECT event_back_card_json_edit 
             FROM $sanas_card_event_table 
             WHERE event_card_id = %d 
               AND event_no = %d",
            $card_id,
            $event_id
        );
        // Execute the query and get the result
        $frontpagedata = $wpdb->get_var($frontpagequery);
        $sanas_portfolio_meta = get_post_meta($card_id,'sanas_metabox',true);
        $frontmetadata=$sanas_portfolio_meta['sanas_back_canavs_image'];
        if (!empty($frontpagedata)) {
            $data = stripslashes(stripslashes(htmlspecialchars_decode($frontpagedata)));
        } 
        else {
            $data = stripslashes(htmlspecialchars_decode($frontmetadata));
        }


        $json_data = $data;
        wp_send_json_success(['json_data' => $json_data]);
    } else {
        wp_send_json_error('Invalid post ID.');
    }

}


// Send Signup Email
    // function sanas_send_signup_email() {
    //     check_ajax_referer('ajax-usersignup-nonce', 'security');
    //     $user_email = sanitize_email($_POST['email']);
        
    //     // Get the email subject and body from theme options
    //     $subject = sanas_options('sanas_user_signup_subject');
    //     $body = sanas_options('sanas_user_signup_body');

    //     // Prepare email headers
    //     $headers = array('Content-Type: text/html; charset=UTF-8');

    //     // Send the email
    //     wp_mail($user_email, $subject, $body, $headers);

    //     wp_die();
    // }

    // add_action('wp_ajax_nopriv_sanas_send_signup_email', 'sanas_send_signup_email');
    // add_action('wp_ajax_sanas_send_signup_email', 'sanas_send_signup_email');


  

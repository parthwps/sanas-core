<?php
/*
 * Plugin Name: Sanas Core
 * Plugin URI: https://thegenius.co
 * Description: Sanas Core Plugin for Sanas Theme
 * Version: 1.0.0
 * Author: Udayraj
 * Author URI: https://wowpixelweb.com
 * Text Domain: sanas-core
 * Domain Path: /languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
define('SANAS_CORE_VERSION', '1.5.0');

global $plugin_domain;
$plugin_domain = 'sanas-core';
if(!defined('SANAS_CODE_PLUGIN_DIR')) {
    define('SANAS_CODE_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
}
if(!defined('SANAS_CODE_PLUGIN_URL')) {
    define('SANAS_CODE_PLUGIN_URL', plugin_dir_url(__FILE__));
}
class Sanas_Code_Plugin_Setup_Plan
{
    static function  init()
    {
        require_once(SANAS_CODE_PLUGIN_DIR.'/core/login-function.php');              
        require_once(SANAS_CODE_PLUGIN_DIR.'/inc/sanas-core-function.php');   
        require_once(SANAS_CODE_PLUGIN_DIR.'/inc/sanas-custom-post-type.php');   
        require_once(SANAS_CODE_PLUGIN_DIR.'/inc/widget-options/sanas-about-widget.php');           
        require_once(SANAS_CODE_PLUGIN_DIR.'/inc/widget-options/sanas-menus-widget.php');  
        require_once(SANAS_CODE_PLUGIN_DIR.'/inc/widget-options/sanas-popular-widget.php');  
        require_once(SANAS_CODE_PLUGIN_DIR.'/inc/meta-box/metabox-init.php');                    
        require_once(SANAS_CODE_PLUGIN_DIR.'/elementor/elementor-setup.php');           
    }
    static  function sanas_plugin_setup_url($u=false)
    {
        if($u){
            $u='/'.$u;
        }
        return plugin_dir_url(__FILE__).$u;
    }
    static  function sanas_plugin_setup_dir($u=false){
        if($u){
            $u='/'.$u;
        }
        return plugin_dir_path(__FILE__).$u;
    }    
}
add_action( 'plugins_loaded', array('Sanas_Code_Plugin_Setup_Plan','init'));
global $setup_go;
$setup_go=new Sanas_Code_Plugin_Setup_Plan();
function Sanas_Code_Plugin_Setup_Plan(){
    return new Sanas_Code_Plugin_Setup_Plan();
}
// Database
function create_sanas_card_event_info_table() {
    global $wpdb;
    $sanas_card_event_table = $wpdb->prefix . 'sanas_card_event'; 
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $sanas_card_event_table (
        event_no INT NOT NULL AUTO_INCREMENT,
        event_user BIGINT(20) UNSIGNED NOT NULL,
        event_card_id BIGINT(20) UNSIGNED NOT NULL,
        event_front_card_json_edit LONGTEXT NOT NULL,
        event_front_card_preview LONGTEXT NOT NULL,
        event_back_card_json_edit LONGTEXT NOT NULL,
        event_back_card_preview LONGTEXT NOT NULL,
        event_rsvp_id BIGINT(20) UNSIGNED NOT NULL,
        event_rsvp_bg_link TEXT NOT NULL,
        event_front_bg_link TEXT NOT NULL,
        event_front_bg_color VARCHAR(250) NOT NULL,
        event_guest_id BIGINT(20) UNSIGNED NOT NULL,
        event_step_id BIGINT(20) UNSIGNED NOT NULL,
        event_status VARCHAR(50) NOT NULL,
        PRIMARY KEY (event_no)
    ) $charset_collate;"; 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function create_sanas_card_guest_info_table() {
    global $wpdb;
    $guest_details_table = $wpdb->prefix . "guest_details_info";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $guest_details_table (
        guest_id int(11) NOT NULL AUTO_INCREMENT, 
        guest_user_id int(100) NOT NULL,
        guest_name varchar(255) NOT NULL,
        guest_event_id varchar(255) NOT NULL,
        guest_group varchar(255) NOT NULL, 
        guest_phone_num varchar(255) NOT NULL,
        guest_email varchar(255) NOT NULL,
        guest_address VARCHAR(255) NOT NULL,
        guest_status varchar(255) NOT NULL,
        PRIMARY KEY (`guest_id`)
    ) $charset_collate AUTO_INCREMENT=1;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
function create_guest_group_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'guest_list_group';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        guest_group_id int(11) NOT NULL AUTO_INCREMENT,
        guest_event_id BIGINT(20) UNSIGNED NOT NULL,
        guest_group_user int(100) NOT NULL,
        guest_group_name varchar(255) NOT NULL,
        PRIMARY KEY (`guest_group_id`)
    ) $charset_collate AUTO_INCREMENT=1;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
} 
// Hook to ensure the table is created on plugin activation 
function sanas_core_plugin_activation() {
      create_sanas_card_event_info_table();
      create_sanas_card_guest_info_table();
      create_guest_group_table();
}
register_activation_hook(__FILE__, 'sanas_core_plugin_activation');

if( ! function_exists( 'sanas_code_social_share_post' ) ){
    function sanas_code_social_share_post(){
        global $wp_query, $post;
        ?>  
      <ul class="social-btns text-right">
        <li><a href="<?php echo esc_url("http://www.facebook.com/"); ?>" target="_blank" class="btn facebook"><i class="fab fa-facebook"></i></a></li>
        <li><a href="<?php echo esc_url("https://x.com/"); ?>" target="_blank" class="btn twitter"><i class="fab fa-twitter"></i></a></li>
        <li><a href="<?php echo esc_url("https://www.instagram.com/"); ?>" target="_blank" class="btn google"><i class="fab fa-instagram"></i></a></li>
        <li><a href="<?php echo esc_url("http://www.linkedin.com/"); ?>" target="_blank" class="btn linkedin"><i class="fab fa-linkedin"></i></a></li>
        <li>
        </li>
      </ul>
        <?php
    }
}   

register_activation_hook(__FILE__, 'sanas_create_wishlist_table');

function sanas_create_wishlist_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sanas_wishlist';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        card_id BIGINT(20) UNSIGNED NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY user_card (user_id, card_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('wp_enqueue_scripts', 'sanas_enqueue_scripts');
function sanas_enqueue_scripts() {
    wp_enqueue_script('sanas-wishlist', plugins_url('js/wishlist.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('sanas-wishlist', 'sanas_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('sanas_wishlist_nonce'),
    ));
}

// Add this code in your plugin's main file or the relevant part where other AJAX functions are defined.
add_action('wp_ajax_toggle_wishlist', 'sanas_toggle_wishlist');
add_action('wp_ajax_nopriv_toggle_wishlist', 'sanas_toggle_wishlist');

function sanas_toggle_wishlist() {
    global $wpdb;
    check_ajax_referer('sanas_wishlist_nonce', 'security');
    $user_id = get_current_user_id();
    $card_id = intval($_POST['card_id']);

    if (!$user_id) {
        wp_send_json_error(['message' => 'You must be logged in to manage your wishlist.']);
    }
    $is_in_wishlist = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}sanas_wishlist WHERE user_id = %d AND card_id = %d",
        $user_id,
        $card_id
    ));

    if ($is_in_wishlist) {
        $result = $wpdb->delete(
            "{$wpdb->prefix}sanas_wishlist",
            array('user_id' => $user_id, 'card_id' => $card_id),
            array('%d', '%d')
        );

        if ($result === false) {
            error_log('Error removing card from wishlist: ' . $wpdb->last_error);
            wp_send_json_error(['message' => 'Failed to remove card from wishlist.']);
        } else {
            wp_send_json_success(['message' => 'Card removed from wishlist', 'action' => 'removed']);
        }
    } else {
        $result = $wpdb->insert(
            "{$wpdb->prefix}sanas_wishlist",
            array('user_id' => $user_id, 'card_id' => $card_id),
            array('%d', '%d')
        );

        if ($result === false) {
            error_log('Error adding card to wishlist: ' . $wpdb->last_error);
            wp_send_json_error(['message' => 'Failed to add card to wishlist.']);
        } else {
            wp_send_json_success(['message' => 'Card added to wishlist', 'action' => 'added']);
        }
    }

    wp_die();
}

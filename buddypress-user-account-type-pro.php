<?php
/**
 * Plugin Name: BuddyPress user account type PRO
 * Plugin URI:  http://wpbpshop.com/buddypress-user-account-type-pro
 * Description: Categories you buddypress users and manage
 * Author:      wp.bp.shop
 * Author URI:  http://wpbpshop.com
 * Version:     1.1.3
 * Text Domain: buddypress
 * License:     GPLv2 or later (license.txt)
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

register_activation_hook( __FILE__,'buatp_activate');
register_deactivation_hook( __FILE__,'buatp_deactivate');

function buatp_activate() { }
function buatp_deactivate() { }

///////////////////////////////////////////////////////////////////////////////

/*
 * Check if buddypress is installed or not
 */
function buatp_checker() {
    if(!is_plugin_active('buddypress/bp-loader.php')):
        echo '<div class="error"><p>';
        echo __('You must need to install and active <b><a href="'.site_url().'/wp-admin/plugin-install.php?tab=search&type=term&s=buddypress&plugin-search-input=Search+Plugins">
        Buddypress</strong></a> to use <strong>Buddypress User Account Type PRO </b> plugin','buatp');
        echo '</p></div>';
    endif;
}
add_action('admin_notices', 'buatp_checker');

///////////////////////////////////////////////////////////////////////////////

define('BUATP_VERSION','1.1.3');
define('BUATP_ROOT',dirname(__FILE__).'/');
define('BUATP_INC',BUATP_ROOT.'_inc/');
define('BUATP_LIB',BUATP_ROOT.'asset/');
define('BUATP_TEMPLATE',BUATP_INC.'templates');
define('BUATP_DIR',basename(dirname(__FILE__)));

define('BUATP_LIB_URI',plugins_url('/asset/', __FILE__));
define('BUATP_CSS_URI',plugins_url('/asset/css/', __FILE__));
define('BUATP_JS_URI',plugins_url('/asset/js/', __FILE__));
define('BUATP_IMG_URI',plugins_url('/asset/images/', __FILE__));
          
function buatp_init(){
// only supported in BP 1.5+
    if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
        require_once (  BUATP_ROOT.'buatp-core-loader.php');
        
        // BUATP custom
        if( file_exists( WP_PLUGIN_DIR.'/buatp-custom.php'   ))
            require_once ( WP_PLUGIN_DIR.'/buatp-custom.php' );

    // show admin notice for users on BP 1.2.x
    } else {
            add_action( 'admin_notices', create_function( '', "
                    echo '<div class=\"error\"><p>' . sprintf( __( \"Hey! BuddyPress User Account Type PRO v1.1 requires BuddyPress 1.5 or higher.  If you are still using BuddyPress 1.2 and you don't plan on upgrading, use BuddyPress User Account Type PRO v1.0.3 or lower, 'buatp' ) ) . '</p></div>';
            " ) );

            return;
    }
}
add_action( 'bp_include', 'buatp_init' );

///////////////////////////////////////////////////////////////////////////////////

function buatp_script_loader(){
    wp_enqueue_script(
		'buat-admin-js',
		BUATP_JS_URI.'admin.js',
                array('jquery')
	);
    wp_localize_script( 'buat-admin-js', 'buatpJsVars', array( 'buatpAjaxUrl' => site_url().'/wp-admin/admin-ajax.php') );
 
}
add_action('wp_enqueue_scripts', 'buatp_script_loader');
add_action('admin_enqueue_scripts','buatp_script_loader');

///////////////////////////////////////////////////////////////////////////////

function buatp_style_loader(){
    wp_register_style( 'buatp-style', BUATP_CSS_URI.'style.css' );
    wp_enqueue_style( 'buatp-style' );
    if( is_admin() || is_network_admin()) {
    wp_register_style( 'buatp-admin-style', BUATP_CSS_URI.'admin-style.css' );
    wp_enqueue_style( 'buatp-admin-style' );
    }
}
add_action( 'wp_enqueue_scripts', 'buatp_style_loader' );
add_action('admin_enqueue_scripts','buatp_style_loader');


////////////////////////////////////////////////////
?>
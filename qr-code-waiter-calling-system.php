<?php
/**
 * The WordPress Plugin ad-landin-page.
 *
 *
 * @package   QR Code Waiter Calling System
 * @author    Catkin <catkin@catzsoft.ee>
 * @copyright 2017 Catkin
 *
 * @wordpress-plugin
 * Plugin Name:       QR Code Waiter Calling System
 * Description:       This is QR Code Waiter Calling System plugin.
 * Version:           20.1216
 * Author:            Catkin
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/**
 * plugin class
 * can be owerwritten by another class, doesn't matter
 * only for purpose of this file
 *
 * @since     1.0.0
 */
$class_name = 'QRCodeWaiterCallingSystem';

/**
 * plugin file
 * can be owerwritten by another class, doesn't matter
 * only for purpose of this file
 *
 * @since     1.0.0
 */
$class_file_name = 'qr-code-waiter-calling-system';


/*
 * 
 *
 */
require_once( plugin_dir_path( __FILE__ ).'wp-plugin/class-'.$class_file_name.'.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
if ( function_exists( 'register_uninstall_hook' ) ){

	register_uninstall_hook( __FILE__, array( $class_name, 'uninstall' ) );
}
register_activation_hook( __FILE__, array( $class_name, 'activate' ) );
register_deactivation_hook( __FILE__, array( $class_name, 'deactivate' ) );

/*
 */
add_action( 'plugins_loaded', array( $class_name, 'get_instance' ) );


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ).'admin/class-'.$class_file_name.'-admin.php' );
	add_action( 'plugins_loaded', array( $class_name.'_Admin', 'get_instance' ) );

}

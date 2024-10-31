<?php
/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @package   QR Code Waiter Calling System
 * @author    Catkin <catkin@catzsoft.ee>
 * @copyright 2014 Catkin
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


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

require_once( plugin_dir_path( __FILE__ ) . 'wp-plugin/class-' . $class_file_name . '.php' );

if (is_multisite()) {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);


	delete_option('qr_code_waiter_calling_system_db_version');

	if ($blogs) {
		foreach($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			delete_option('qr_code_waiter_calling_system_db_version');
			
			//info: remove and optimize tables
			eval("" . $class_name . "::uninstall();");		
			
			$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
			
			restore_current_blog();
		}
	}
}
else
{
	delete_option('qr_code_waiter_calling_system_db_version');
	//info: remove and optimize tables
	eval("" . $class_name . "::uninstall();");	
	$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
	
}
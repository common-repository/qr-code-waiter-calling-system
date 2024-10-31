<?php
/**
 * qr-code-waiter-calling-system.
 *
 * Database setting for plugin
 *
 * @package qr-code-waiter-calling-system
 * @author  Catkin <catkin@catzsoft.ee>
 */

global $wpdb;
global $qr_code_waiter_calling_system_db_table_option;
global $qr_code_waiter_calling_system_db_version;

//basic settings
$qr_code_waiter_calling_system_db_version = "1.0";
$qr_code_waiter_calling_system_db_prefix  = $wpdb->prefix."qr_code_waiter_calling_system";

$qr_code_waiter_calling_system_db_table_option = $qr_code_waiter_calling_system_db_prefix."_options";

//tables in group for simple manipulation
global $qr_code_waiter_calling_system_db_tables;
$qr_code_waiter_calling_system_db_tables = array( 'qr_code_waiter_calling_system_db_table_option' );


/**
 * Drop tables
 *
 */
function qr_code_waiter_calling_system_uninstall_db_drop_tables() {

	global $qr_code_waiter_calling_system_db_tables;

	foreach ( $qr_code_waiter_calling_system_db_tables as $table ) {
		$GLOBALS['wpdb']->query( "DROP TABLE `".$GLOBALS[ $table ]."`" );
	}
}

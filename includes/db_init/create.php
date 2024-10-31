<?php
/**
 * QR Code Waiter Calling System.
 * 
 * Database create tables
 * 
 * @package qr-code-waiter-calling-system
 * @author  Catkin <catkin@catzsoft.ee>
 */
 
 	require_once(dirname(__FILE__) . '/'. '../db.php');
 
  //Plugin table options
	global $qr_code_waiter_calling_system_db_table_option;
	global $qr_code_waiter_calling_system_db_create_option;
	global $qr_code_waiter_calling_system_db_version;
		
	//basic settings
	$qr_code_waiter_calling_system_db_version = "1.0";
	  
	$qr_code_waiter_calling_system_db_create_option = "CREATE TABLE $qr_code_waiter_calling_system_db_table_option (
  id int(11) NOT NULL AUTO_INCREMENT,
  active int(11) NOT NULL,
  api_key VARCHAR(50) NOT NULL,
  api_call_url VARCHAR(1000) DEFAULT '' NOT NULL,
  http_logo_filename VARCHAR(255) NULL,
  local_logo_filename VARCHAR(255) DEFAULT '' NULL,
  PRIMARY KEY (id)
    );";
	
	
	
	$qr_code_waiter_calling_system_db_table_create = array('qr_code_waiter_calling_system_db_create_option');

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	foreach ( $qr_code_waiter_calling_system_db_table_create as $table_create )
		dbDelta( $GLOBALS[$table_create] );
	
	add_option( "qr_code_waiter_calling_system_db_version", $qr_code_waiter_calling_system_db_version );

	
	
<?php

/**
 * QR Code Waiter Calling System.
 *
 * @package QR Code Waiter Calling System
 * @author  Catkin <catkin@catzsoft.ee>
 */
class QRCodeWaiterCallingSystem_Public {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Wrong api key
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const WRONGKEY = '';

	/**
	 * Remote api url
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $remote_api_url = 'https://wpapi20200511002740.azurewebsites.net/Place';

	/**
	 * Remote api action url
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $remote_api_action_url = 'https://wpapi20200511002740.azurewebsites.net/Action';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_styles() {
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_scripts() {
	}

	/**
	 * Custom activation
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( self::installed() ) {
			self::activateWaiterCallingLandingPage( 1 );
		} else {
			self::install();
		}
	}

	/**
	 * Custom deactivation
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		self::activateWaiterCallingLandingPage( 0 );
	}

	/**
	 * Is database installed
	 *
	 * @since    1.0.0
	 */
	private static function installed() {
		global $wpdb;

		$tableindb = $wpdb->get_results( "SHOW TABLES LIKE '".$GLOBALS['qr_code_waiter_calling_system_db_table_option']."'" );

		return ( count( $tableindb ) > 0 );
	}

	/**
	 * Custom instalation
	 *
	 * @since    1.0.0
	 */
	public static function install() {


		//create table in DB
		//include_once( basename( plugin_dir_path( dirname( __FILE__ ) ) )  . '/includes/db_init/create.php' );
		include_once( plugin_dir_path( __FILE__ )."db_init/create.php" );

		//get key from api
		self::getKey();
	}

	public static function getKey()
	{
		global $wpdb;
		$key = self::getKeyCallRemoteApi();

		//set active
		$active = 1;
		if ( self::apiKeyWrong( $key ) ) {
			$active = 0;
		}

		//get api url
		$api_call_url = self::getProxyURL2ApiAction( $key );

		//saving key into db
		$wpdb->insert( $GLOBALS['qr_code_waiter_calling_system_db_table_option'], array(
			'api_key'      => $key,
			'active'       => $active,
			'api_call_url' => $api_call_url
		) );

	}

	/**
	 * Custom uninstalation
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		include_once( plugin_dir_path( __FILE__ ).'db.php' );

		//call api
		$result = self::callAPI( "DELETE", true, false );

		//delete logo file
		self::setLOGOfilename();

		//drop tables
		qr_code_waiter_calling_system_uninstall_db_drop_tables();
	}

	/**
	 * get url to proxy and to api
	 *
	 * @since    1.0.0
	 */
	public static function resetApiKey() {
		global $qr_code_waiter_calling_system_db_table_option;
		global $wpdb;

		//get key from api
		$key = self::getKeyCallRemoteApi();

		//set active
		$active = 1;
		if ( self::apiKeyWrong( $key ) ) {
			$active = 0;
		}

		//get api url
		$api_call_url = self::getProxyURL2ApiAction( $key );

		//sql update
		$wpdb->update(
			$qr_code_waiter_calling_system_db_table_option,
			array(
				'api_key'      => $key,
				'active'       => $active,
				'api_call_url' => $api_call_url
			),
			array( 'ID' => 1 ),
			array(
				'%s',
				'%d',
				'%s'
			),
			array( '%d' )
		);

	}

	/**
	 * get url to proxy and to api
	 *
	 * @since    1.0.0
	 */
	public static function getProxyURL2ApiAction( $key ) {

		//proxy url
		$proxy_url = plugins_url( 'qr-code-waiter-calling-system/public/FileProxy.php' );

		//get api url
		$api_url = self::$remote_api_action_url;

		$api_call_url = $proxy_url."?url=".rawurlencode( $api_url );

		return $api_call_url;
	}

	/**
	 * call remote api
	 *
	 * @since    1.0.0
	 */
	public static function callAPI( $method, $with_key, $active = true ) {

		//call api
		$site_name   = get_bloginfo( 'name' );
		$web_page    = site_url();
		$admin_email = get_bloginfo( 'admin_email' );

		//get api url
		$api_url = self::$remote_api_url;

		//add key
		if ( $with_key ) {
			$api_url .= '/'.self::getAPIkey();
		}

		//add variables
		$data = array(
			"Name"   => $site_name,
			"Web"    => $web_page,
			"Email"  => $admin_email,
			"Active" => $active
		);
		//set options/ headers
		$options = array(
			'http' => array(
				'method'  => $method,
				'content' => json_encode( $data ),
				'header'  => "Content-Type: application/json\r\n".
				             "Accept: application/json\r\n"
			)
		);

		
		//call api
		$context = stream_context_create( $options );
		$result  = @file_get_contents( $api_url, false, $context );

		return $result;
	}

	/**
	 * call remote api
	 *
	 * @since    1.0.0
	 */
	public static function getKeyCallRemoteApi() {

		$result = self::callAPI( "POST", false );

		//parsing key from result when api will work
		if ( $result === false ) {
			$key = self::WRONGKEY;
		} else {
			$key = str_replace( '"', '', $result );
		}

		return $key;
	}

	/**
	 * check if api key is wrong
	 *
	 * @since    1.0.0
	 */
	public static function apiKeyWrong( $key ) {
		return ( $key == self::WRONGKEY );
	}

	/**
	 * get API call URL
	 *
	 * @since    1.0.0
	 */
	public static function getAPIcallURL() {
		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;

		return $wpdb->get_var( "select api_call_url from $qr_code_waiter_calling_system_db_table_option" );
	}

	/**
	 * get APIkey
	 *
	 * @since    1.0.0
	 */
	public static function getAPIkey() {
		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;

		return $wpdb->get_var( "select api_key from $qr_code_waiter_calling_system_db_table_option" );
	}

	public static function getLanguage() {
		return "en";
	}

	/**
	 * is public QR Code Waiter Calling System active
	 *
	 * @since    1.0.0
	 */
	public static function isActive() {
		include_once( ABSPATH.'wp-admin/includes/plugin.php' );

		// check for plugin using plugin name
		if ( ! is_plugin_active( 'qr-code-waiter-calling-system/qr-code-waiter-calling-system.php' ) ) {
			//plugin is not activated
			return false;
		}

		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;

		return $wpdb->get_var( "select active from $qr_code_waiter_calling_system_db_table_option" );
	}

	/**
	 * get logo filename
	 *
	 * @since    1.0.0
	 */
	public static function getLOGOfilename() {
		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;
		$logo_filename = $wpdb->get_var( "select http_logo_filename from $qr_code_waiter_calling_system_db_table_option" );
		if ( is_null( $logo_filename ) ) {
			$logo_filename = '';
		}

		return $logo_filename;
	}

	/**
	 * get logo filename local
	 *
	 * @since    1.0.0
	 */
	public static function getLOGOfilenameLocal() {
		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;
		$logo_filename = $wpdb->get_var( "select local_logo_filename from $qr_code_waiter_calling_system_db_table_option" );
		if ( is_null( $logo_filename ) ) {
			$logo_filename = '';
		}

		return $logo_filename;
	}

	/**
	 * set logo filename
	 *
	 * @since    1.0.0
	 */
	public static function setLOGOfilename( $http_logo_filename = '', $local_logo_filename = '' ) {
		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;
		//delete old image
		$old_local_logo_filename = $wpdb->get_var( "select local_logo_filename from $qr_code_waiter_calling_system_db_table_option" );

		//delete old file if it's not the same file
		if ( ( $old_local_logo_filename != $local_logo_filename ) && ( $old_local_logo_filename != '' ) ) {
			if ( file_exists( $old_local_logo_filename ) ) {
				@unlink( $old_local_logo_filename );
			}
		}

		//sql update
		$wpdb->update(
			$qr_code_waiter_calling_system_db_table_option,
			array(
				'http_logo_filename'  => $http_logo_filename,
				'local_logo_filename' => $local_logo_filename
			),
			array( 'ID' => 1 ),
			array(
				'%s',
				'%s'
			),
			array( '%d' )
		);
	}

	/**
	 * (de)activate landing page
	 *
	 * @since    1.0.0
	 */
	public static function activateWaiterCallingLandingPage( $status = 1 ) {

		
		if ( self::isActiveRewrite() ) {
			//call api
			$result = self::callAPI( "PUT", true, $status);

			global $wpdb;
			global $qr_code_waiter_calling_system_db_table_option;

			//sql update
			$wpdb->update(
				$qr_code_waiter_calling_system_db_table_option,
				array(
					'active' => $status
				),
				array( 'ID' => 1 ),
				array( '%d' ),
				array( '%d' )
			);

		} else {
			self::getKey();
		}
	}

	public static function isActiveRewrite() {
		global $wpdb;
		global $qr_code_waiter_calling_system_db_table_option;
		return $wpdb->get_var( "SELECT COUNT(*) AS `count` from `$qr_code_waiter_calling_system_db_table_option`" );
	}
}
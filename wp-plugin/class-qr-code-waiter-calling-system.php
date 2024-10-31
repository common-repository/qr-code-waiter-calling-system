<?php
/**
 * QR Code Waiter Calling System.
 *
 * @package QR Code Waiter Calling System
 * @author  Catkin <catkin@catzsoft.ee>
 */
class QRCodeWaiterCallingSystem {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for plugin. Will be used for class file include
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected static $plugin_slug = 'qr-code-waiter-calling-system';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Instance of class with plugin functions -fron end.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance_plugin = null;	
	
	/**
	 * Execute Public functions on pages with shortcode
	 *
	 * @since     1.0.0
	 */	
	protected static $only_short_code_pages = false;	

	/**
	 * Execute Public class Name
	 *
	 * @since     1.0.0
	 */	
	protected static $class_name = 'QRCodeWaiterCallingSystem';		
	
	/**
	 * ShortCodes definition
	 *
	 * @since     1.0.0
	 */
	protected static $short_codes = array( );			
	
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.		
	    if(self::$only_short_code_pages){
			add_filter('the_posts', array( $this, 'conditional_enqueue_files' ) ); // the_posts gets triggered before wp_head
		}else{
			self::enqueue_files();
		}

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public static function get_plugin_slug() {
		return self::$plugin_slug;
	}
	
	/**
	 * Return the class name.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin class name variable.
	 */
	public static function get_plugin_class_name() {
		return self::$class_name;
	}	
	
	/**
	 * Return the public class name.
	 *
	 * @since    1.0.0
	 *
	 * @return    Public Plugin class name variable.
	 */
	public static function get_plugin_public_class_name() {
		return self::get_plugin_class_name() . '_Public';
	}	
		
	

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}
	
	/**
	 * Check if we are on "shortcoded" page and add enqueue styles and scripts and public plugin class
	 *
	 * @since    1.0.0
	 *
	 */
	public static function conditional_enqueue_files($posts){
		if (empty($posts)) return $posts;

		$short_code_page_hook = false;
		//checking all posts for shortcode
		foreach ($posts as $post) {
			//check each shortcode
			foreach(self::$short_codes as $short_code => $function ){
				if (stripos($post->post_content, '['. $short_code .'/]') !== false) {
					$short_code_page_hook = true; // bingo!
					break;
				}
			}
			//break foreach if we have short_code_page_hook
			if($short_code_page_hook) break;
		}

		if ($short_code_page_hook) {
			// enqueue here
			self::enqueue_files();
		}
	 
		return $posts;
	}  	

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		include_once(plugin_dir_path( __FILE__ ) . '../includes/class-public-' . self::get_plugin_slug() . '.php');
		include_once(plugin_dir_path( __FILE__ ) . '../includes/db.php');
		eval("" . self::get_plugin_public_class_name() . "::activate();");		
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		include_once(plugin_dir_path( __FILE__ ) . '../includes/class-public-' . self::get_plugin_slug() . '.php');
		
		eval("" . self::get_plugin_public_class_name() . "::deactivate();");			
		
	}
	
	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		include_once(plugin_dir_path( __FILE__ ) . '../includes/class-public-' . self::get_plugin_slug() . '.php');
		eval("" . self::get_plugin_public_class_name() . "::uninstall();");
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = self::$plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}
	
	/**
	 * Register and enqueue public-facing style sheet, JavaScript files and public plugin class.
	 *
	 * @since    1.0.0
	 */	
	public static function enqueue_files() {
		include_once(plugin_dir_path( __FILE__ ) . '../includes/class-public-' . self::get_plugin_slug() . '.php');
					
		add_action( 'wp_enqueue_scripts', array( self::get_plugin_public_class_name(), 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( self::get_plugin_public_class_name(), 'enqueue_scripts' ) );
		
		eval("\$instance_plugin = new " . self::get_plugin_public_class_name() . "();");
		
		// Add shortcodes
		foreach(self::$short_codes as $short_code => $function ){
			add_shortcode($short_code, array( $instance_plugin, $function ));
		}		
	}	
}
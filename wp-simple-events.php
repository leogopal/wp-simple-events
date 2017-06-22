<?php
/**
 * WP Simple Events for WordPress
 *
 * @link              https://leogopal.com/
 * @since             1.0.0
 * @package           WP Simple Events
 *
 * Plugin Name: WP Simple Events
 * Plugin URI:  https://leogopal.com/
 * Description: Basic simple events listing for WordPress
 * Version:     0.0.1
 * Author:      Leo Gopal
 * Author URI:  https://leogopal.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpse
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class WP_Simple_Events {

	/** Refers to a single instance of this class. */
	private static $instance = null;

	private $version = '0.0.1';

	public $prefix = 'wpse_';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  WP_Simple_Events A single instance of this class.
	 */
	public static function instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {
		$this->define_constants();
		$this->init_core();
	}

	/**
	 * Defining the plugin constants
	 */
	public function define_constants() {
		$this->define( 'WPSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'WPSE_PREFIX', $this->prefix );
		$this->define( 'WPSE_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Instantiate the plugin core and load main plugin file.
	 */
	public function init_core() {
		include_once( 'includes/class-wp-simple-events-plugin.php' );
		new WP_Simple_Events_Plugin();
	}

} // end class

// Load Plugin for Simple Events
WP_Simple_Events::instance();

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

class WPSimpleEvents {

	/** Refers to a single instance of this class. */
	private static $instance = null;

	private $version = '1.0.0';

	public $prefix = 'wpse_';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  WPSimpleEvents A single instance of this class.
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
		$this->init();
	}

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

	public function init() {
		include_once( 'includes/class-wp-simple-events-plugin.php' );
		new WPSE_Plugin();
	}

} // end class

WPSimpleEvents::instance();

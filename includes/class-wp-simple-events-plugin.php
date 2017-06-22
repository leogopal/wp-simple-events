<?php
/**
 * WP Simple Events post type
 *
 * @link              https://leogopal.com/
 * @since             0.0.1
 * @package           WP Simple Events
 *
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class WP_Simple_Events_Plugin {

	public $fields;

	public function __construct() {
		$this->fields = $this->get_custom_fields();
		$this->init();
	}

	/**
	 * Loads all the add_actions necessary to load the plugin
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );
		add_action( 'rest_api_init', array( $this, 'register_custom_meta_to_rest' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
	}

	/**
	 * Registers the main events custom post type
	 */
	public function register_post_types() {
		$labels = array(
			'name'                  => _x( 'Events', 'Post Type General Name', 'wpse' ),
			'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'wpse' ),
			'menu_name'             => __( 'Simple Events', 'wpse' ),
			'name_admin_bar'        => __( 'Event', 'wpse' ),
			'archives'              => __( 'Event Archives', 'wpse' ),
			'attributes'            => __( 'Event Attributes', 'wpse' ),
			'parent_item_colon'     => __( 'Parent Event:', 'wpse' ),
			'all_items'             => __( 'All Events', 'wpse' ),
			'add_new_item'          => __( 'Add New Event', 'wpse' ),
			'add_new'               => __( 'Add New', 'wpse' ),
			'new_item'              => __( 'New Event', 'wpse' ),
			'edit_item'             => __( 'Edit Event', 'wpse' ),
			'update_item'           => __( 'Update Event', 'wpse' ),
			'view_item'             => __( 'View Event', 'wpse' ),
			'view_items'            => __( 'View Events', 'wpse' ),
			'search_items'          => __( 'Search Event', 'wpse' ),
			'not_found'             => __( 'Not found', 'wpse' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wpse' ),
			'featured_image'        => __( 'Featured Image', 'wpse' ),
			'set_featured_image'    => __( 'Set featured image', 'wpse' ),
			'remove_featured_image' => __( 'Remove featured image', 'wpse' ),
			'use_featured_image'    => __( 'Use as featured image', 'wpse' ),
			'insert_into_item'      => __( 'Insert into Event', 'wpse' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Event', 'wpse' ),
			'items_list'            => __( 'Events list', 'wpse' ),
			'items_list_navigation' => __( 'Events list navigation', 'wpse' ),
			'filter_items_list'     => __( 'Filter items Event', 'wpse' )
		);

		$args = array(
			'label'               => __( 'Event', 'wpse' ),
			'description'         => __( 'Simple event listing for WordPress', 'wpse' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true
		);

		register_post_type( 'simple_event', $args );
	}

	/**
	 * Adds a custom meta box to the Events custom post type
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'simple-event-information',
			esc_html__( 'Simple Events', 'wpse' ),
			array( $this, 'simple_event_info_meta_box' ),
			'simple_event',
			'normal',
			'high'
		);
	}

	/**
	 * Display the custom metabox and related custom fields.
	 * @param $post
	 */
	public function simple_event_info_meta_box( $post ) {

		wp_nonce_field( 'simple_event_info', 'simple_event_info_nonce' );

		echo '<table class="form-table">';

		foreach ( $this->fields as $field ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
			// begin a table row wit
			echo '<tr> 
				<th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th> 
				<td>';
			echo '<input type="' . $field['type'] . '" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $value . '" size="30" />';
			echo '<br /><span class="description">' . $field['desc'] . '</span>';
			echo '</td></tr>';
		} // end foreach

		echo '</table>'; // end table
	}

	/**
	 * Save custom meta fields
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save_meta( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['simple_event_info_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['simple_event_info_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'simple_event_info' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) :
			return $post_id;
		endif;

		if ( ! current_user_can( 'edit_post', $post_id ) ) :
			return $post_id;
		endif;

		foreach ( $this->fields as $field ) {
			$old_value = get_post_meta( $post_id, $field['id'], true );
			$new_value = sanitize_text_field( $_POST[ $field['id'] ] );

			if ( $new_value && $new_value != $old_value ) {
				update_post_meta( $post_id, $field['id'], $new_value );
			} elseif ( '' == $new_value && $old_value ) {
				delete_post_meta( $post_id, $field['id'], $old_value );
			}
		}
	}

	/**
	 * Calls the necessay admin scripts (js)
	 */
	public function register_admin_scripts() {
		wp_enqueue_script(
			'wp-jquery-date-picker',
			WPSE_PLUGIN_URL . '/assets/js/admin.js',
			array( 'jquery', 'jquery-ui-datepicker' )
		);
	}

	/**
	 * Returns an array of custom fields to be used through out plugin.
	 *
	 * @return array
	 */
	public function get_custom_fields() {

		$fields = array(
			array(
				'label' => 'Event Date',
				'desc'  => 'Select the date of the event.',
				'id'    => WPSE_PREFIX . 'date',
				'type'  => 'date',
			),
			array(
				'label' => 'Ticket Price',
				'desc'  => 'Enter the ticket price for the event.',
				'id'    => WPSE_PREFIX . 'ticket_price',
				'type'  => 'number',
			),
			array(
				'label' => 'Number of Available Tickets',
				'desc'  => 'Enter number of available tickets for the event.',
				'id'    => WPSE_PREFIX . 'number_available_tickets',
				'type'  => 'number',
			)
		);

		return $fields;

	}

	/**
	 * A simple REST function to be used when adding custom meta fields to WordPress
	 *
	 * @param $object
	 * @param $field_name
	 * @param $request
	 *
	 * @return mixed
	 */
	public function restfully_get_custom_meta( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}

	/**
	 * Uses the 'restfully_get_custom_meta' function
	 * to get and expose the custom fields to the API.
	 */
	public function register_custom_meta_to_rest() {

		foreach ( $this->fields as $field ) {
			register_rest_field(
				'simple_event',
				$field['id'],
				array(
					'get_callback'    => array( $this, 'restfully_get_custom_meta' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
		}

	}

}
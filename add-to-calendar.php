<?php

/*
Plugin Name: Add To Google Calendar Link Generator
Plugin URI: http://www.groundhogg.io
Description: Generate a link which will allows users to add events to their Google Calendar
Version: 1.0
Author: Adrian Tobey
Author URI: httsz://www.groundhogg.io
License: A "Slug" license name e.g. GPL2
*/

define( 'ADD_TO_CALENDAR_VERSION', '1.0.0' );
define( 'ADD_TO_CALENDAR_PREVIOUS_STABLE_VERSION', '0.0.0' );

define( 'ADD_TO_CALENDAR__FILE__', __FILE__ );
define( 'ADD_TO_CALENDAR_PLUGIN_BASE', plugin_basename( ADD_TO_CALENDAR__FILE__ ) );
define( 'ADD_TO_CALENDAR_PATH', plugin_dir_path( ADD_TO_CALENDAR__FILE__ ) );

define( 'ADD_TO_CALENDAR_URL', plugins_url( '/', ADD_TO_CALENDAR__FILE__ ) );

define( 'ADD_TO_CALENDAR_ASSETS_PATH', ADD_TO_CALENDAR_PATH . 'assets/' );
define( 'ADD_TO_CALENDAR_ASSETS_URL', ADD_TO_CALENDAR_URL . 'assets/' );


/**
 * Class Add_To_Google_Calendar
 */
class Add_To_Google_Calendar {

	public static $DATEFORMAT = 'Ymd\THis\Z';

	/**
	 * Add_To_Google_Calendar constructor.
	 */
	public function __construct() {
		add_shortcode( 'add_to_cal', [ $this, 'shortcode' ] );
		add_action( 'admin_menu', [ $this, 'register_admin_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_ajax_generate_add_to_calendar', [ $this, 'ajax_handler' ] );
	}

	/**
	 * Get a post parameter
	 *
	 * @param $key
	 *
	 * @param bool $default
	 *
	 * @return mixed
	 */
	protected function get_post_param( $key, $default = false ) {
		if ( isset( $_POST[ $key ] ) && ! empty( $_POST[ $key ] ) ) {
			return wp_unslash( $_POST[ $key ] );
		}

		return $default;
	}

	/**
	 * Get the WP Offset time
	 *
	 * @param bool $in_seconds
	 *
	 * @return float|int
	 */
	protected function get_wp_offset( $in_seconds = true ) {
		$offset = intval( get_option( 'gmt_offset' ) );

		if ( $in_seconds ) {
			$offset = $offset * HOUR_IN_SECONDS;
		}

		return $offset;
	}

	/**
	 * Convert a unix timestamp to UTC-0 time
	 *
	 * @param $time
	 *
	 * @return int
	 */
	public function convert_to_utc_0( $time ) {
		if ( is_string( $time ) ) {
			$time = strtotime( $time );
		}

		return $time - $this->get_wp_offset();

	}

	/**
	 * @param string $part
	 *
	 * @return int|false
	 */
	protected function get_correct_time( $part = 'start' ) {
		$date = sanitize_text_field( $this->get_post_param( $part . '_date' ) );
		$time = sanitize_text_field( $this->get_post_param( $part . '_time' ) );

		if ( ! $date || ! $time ) {
			return false;
		}

		$time_string = sprintf( "%s %s", $date, $time );
		$time        = $this->convert_to_utc_0( strtotime( $time_string ) );

		return date( 'Y-m-d H:i:s', $time );
	}


	/**
	 * Handle the Ajax action
	 */
	public function ajax_handler() {

        if ( ! wp_verify_nonce( $this->get_post_param( '_wpnonce' ) ) || ! current_user_can( 'edit_posts' ) ){
            return;
        }

		$args = [
			'start'    => $this->get_correct_time( 'start' ),
			'end'      => $this->get_correct_time( 'end' ),
			'title'    => sanitize_text_field( $this->get_post_param( 'title' ) ),
			'desc'     => sanitize_text_field( $this->get_post_param( 'desc' ) ),
			'location' => sanitize_text_field( $this->get_post_param( 'location' ) ),
			'text'     => sanitize_text_field( $this->get_post_param( 'text' ) ),
		];

		$link      = $this->render_link( $args );
		$shortcode = $this->render_shortcode( $args );

		wp_send_json_success( [
			'link'      => esc_url( $link ),
			'html'      => esc_html( do_shortcode( $shortcode ) ),
			'shortcode' => esc_html( $shortcode ),
		] );
	}

	/**
	 * Build an add to google calendar link
	 *
	 * @param $args
	 *
	 * @return string
	 */
	protected function render_link( $args ) {
		$args = wp_parse_args( $args, [
			'start'    => date( 'Y-m-d H:i:s' ),
			'end'      => date( 'Y-m-d H:i:s', time() + HOUR_IN_SECONDS ),
			'title'    => '',
			'desc'     => '',
			'location' => '',
		] );

		$start = strtotime( $args['start'] );
		$end   = strtotime( $args['end'] );

		$start_formatted = date( self::$DATEFORMAT, $start );
		$end_formatted   = date( self::$DATEFORMAT, $end );

		$link = add_query_arg( urlencode_deep( [
			'action'   => 'TEMPLATE',
			'text'     => $args['title'],
			'details'  => $args['desc'],
			'location' => $args['location'],
			'dates'    => $start_formatted . '/' . $end_formatted
		] ), 'https://www.google.com/calendar/render' );

		return $link;
	}

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	protected function render_shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'start'    => date( 'Y-m-d H:i:s' ),
			'end'      => date( 'Y-m-d H:i:s', time() + HOUR_IN_SECONDS ),
			'title'    => '',
			'desc'     => '',
			'location' => '',
			'text'     => __( 'Add To Google Calendar', 'add-to-cal' ),
			'raw'      => false
		), $atts );

		$inner = "";

		foreach ( $atts as $key => $value ) {

			if ( empty( $value ) ) {
				continue;
			}

			$key = strtolower( $key );

			$inner .= sanitize_key( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return sprintf( "[add_to_cal %s]", $inner );
	}


	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function shortcode( $atts = [] ) {

		$atts = shortcode_atts( array(
			'start'    => date( 'Y-m-d H:i:s' ),
			'end'      => date( 'Y-m-d H:i:s', time() + HOUR_IN_SECONDS ),
			'text'     => __( 'Add To Google Calendar', 'add-to-cal' ),
			'title'    => '',
			'desc'     => '',
			'location' => '',
			'raw'      => false
		), $atts );

		$is_raw = boolval( $atts['raw'] );

		$event = [
			'text' => $atts['text'],
			'link' => $this->render_link( $atts )
		];

		$event = apply_filters( 'add_to_calendar_shortcode', $event, $atts );

		if ( $is_raw ) {
			return $event['link'];
		}

		return sprintf( "<a class=\"add-to-calendar\" href=\"%s\" target=\"_blank\">%s</a>", esc_url( $event['link'] ), $event['text'] );
	}

	/**
	 * Register the admins scripts
	 */
	public function register_scripts() {
		wp_register_style( 'add-to-cal', ADD_TO_CALENDAR_ASSETS_URL . 'style.css', [] );
		wp_register_script( 'add-to-cal', ADD_TO_CALENDAR_ASSETS_URL . 'generate.js', [ 'jquery' ] );
	}

	/**
	 * Register the admin page
	 */
	public function register_admin_page() {
		$page = add_submenu_page(
			'tools.php',
			__( 'Add To Calendar', 'add-to-cal' ),
			__( 'Add To Calendar', 'add-to-cal' ),
			'edit_posts',
			'add_to_calendar',
			[ $this, 'page' ]
		);
	}

	/**
	 * Render the page
	 */
	public function page() {
		wp_enqueue_style( 'add-to-cal' );
		wp_enqueue_script( 'add-to-cal' );

		?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e( 'Add To Calendar', 'add-to-cal' ) ?></h1>
            <hr class="wp-header-end">
			<?php

			include dirname( __FILE__ ) . '/form.php'

			?>
        </div>
		<?php
	}
}

new Add_To_Google_Calendar();
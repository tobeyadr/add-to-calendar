<?php

namespace GroundhoggAddToCalendar;

use Groundhogg\Admin\Admin_Menu;
use Groundhogg\DB\Manager;
use Groundhogg\Extension;

class Plugin extends Extension {


	/**
	 * Override the parent instance.
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Include any files.
	 *
	 * @return void
	 */
	public function includes() {

	}

	/**
	 * Init any components that need to be added.
	 *
	 * @return void
	 */
	public function init_components() {
		new Add_To_Calendar();
	}

	/**
	 * Get the ID number for the download in EDD Store
	 *
	 * @return int
	 */
	public function get_download_id() {
		return 39872;
	}

	/**
	 * Get the version #
	 *
	 * @return mixed
	 */
	public function get_version() {
		return GROUNDHOGG_ADD_TO_CALENDAR_VERSION;
	}

	/**
	 * @return string
	 */
	public function get_plugin_file() {
		return GROUNDHOGG_ADD_TO_CALENDAR__FILE__;
	}

	/**
	 * Register the admin scripts
	 *
	 * @param bool $is_minified
	 * @param string $dot_min
	 */
	public function register_admin_scripts( $is_minified, $dot_min ) {
		wp_register_style( 'groundhogg-add-to-cal', GROUNDHOGG_ADD_TO_CALENDAR_ASSETS_URL . 'style.css', [] );
		wp_register_script( 'groundhogg-add-to-cal', GROUNDHOGG_ADD_TO_CALENDAR_ASSETS_URL . 'generate.js', [ 'jquery' ] );
	}

	/**
	 * Register autoloader.
	 *
	 * Groundhogg autoloader loads all the classes needed to run the plugin.
	 *
	 * @since 1.6.0
	 * @access private
	 */
	protected function register_autoloader() {
		require GROUNDHOGG_ADD_TO_CALENDAR_PATH . 'includes/autoloader.php';
		Autoloader::run();
	}
}

Plugin::instance();
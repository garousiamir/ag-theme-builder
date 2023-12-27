<?php
/**
 * Plugin Name: Ag Theme Builder
 * Plugin URI:  https://agarousi.com
 * Description: Free Theme Builder for Elementor
 * Author:      Amirhossein Garousi
 * Author URI:  https://agarousi.com
 * Version:     1.0
 * Developer:   Amirhossein Garousi
 * Text Domain: ag-theme-builder
 * Elementor tested up to: 3.17.1
 *
 * @package ag-theme-builder
 */

define( 'AG_THEME_BUILDER_VER', '1.0' );
define( 'AG_THEME_BUILDER_FILE', __FILE__ );
define( 'AG_THEME_BUILDER_BASE', plugin_basename( __FILE__ ) );
define( 'AG_THEME_BUILDER_DIR', plugin_dir_path( AG_THEME_BUILDER_FILE ) );
define( 'AG_THEME_BUILDER_URL', plugins_url( '/', __FILE__ ) );


final class Ag_Theme_Builder {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );

		//Fires when Ag Theme Builder was fully loaded
		do_action( 'ag_theme_builder_loaded' );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain(
			'ag-theme-builder',
			false,
			dirname( plugin_basename( AG_THEME_BUILDER_FILE ) ) . '/language/'
		);
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once plugin_dir_path( __FILE__ ) . 'plugin.php';

	}



}

// Instantiate Ag_Theme_Builder.
new Ag_Theme_Builder();

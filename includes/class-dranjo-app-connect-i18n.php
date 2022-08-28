<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://dranjo.com
 * @since      1.0.0
 *
 * @package    dranjo_app_connect
 * @subpackage dranjo_app_connect/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    dranjo_app_connect
 * @subpackage dranjo_app_connect/includes
 * @author     Abdul Hakeem <hakeem.nala@gmail.com>
 */
class dranjo_app_connect_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'build-app-online',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

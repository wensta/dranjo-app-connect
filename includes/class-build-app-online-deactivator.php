<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://dranjo.com
 * @since      1.0.0
 *
 * @package    build_app_online
 * @subpackage build_app_online/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    build_app_online
 * @subpackage build_app_online/includes
 * @author     Dranjo <support@dranjo.com>
 */
class build_app_online_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

	  // Remove the rewrite rule on deactivation
	  global $wp_rewrite;
	  $wp_rewrite->flush_rules();

	}

}

<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://dranjo.com
 * @since      1.0.0
 *
 * @package    dranjo_connect
 * @subpackage dranjo_connect/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    dranjo_connect
 * @subpackage dranjo_connect/includes
 * @author     Dranjo <support@dranjo.com>
 */
class dranjo_connect_Deactivator {

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

<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP API Search
 * @subpackage wp-api-search/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP API Search
 * @subpackage wp-api-search/includes
 * @author     Corey Burns <coreyaburns@gmail.com>
 */
class WP_API_Search_Deactivator {

	/**
	 * Removing shortcode.
	 *
	 * @since    0.0.1
	 */
	public static function deactivate() {
		remove_shortcode( 'wp-api-search-results' );
	}

}

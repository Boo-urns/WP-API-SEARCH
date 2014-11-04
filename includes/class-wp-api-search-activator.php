<?php

/**
 * Fired during plugin activation
 *
 * @since      0.0.1
 *
 * @package    WP API Search
 * @subpackage WP API Search/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    WP API Search
 * @subpackage WP API Search/includes
 * @author     Corey Burns <coreyaburns@gmail.com>
 */
class WP_API_Search_Activator {

	/**
	 * Common words to be ignored
	 *
	 * Sets up option for common words to be ignored.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/func-initial-common-words.php';
		$common_words = common_words();

		add_option('common_words_ignored', $common_words);
		// add_option does not overwrite if this plugin has been enabled previously.
	}

	
}

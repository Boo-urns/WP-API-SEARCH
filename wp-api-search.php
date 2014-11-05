<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       WP API Search
 * Plugin URI:        http://simantel.com
 * Description:       Utilizing WP API to create a custom search through posts, pages, custom post types. Also ties in with google custom search for spelling suggestions.
 * Version:           0.1
 * Author:            Corey Burns
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-api-search-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-api-search-deactivator.php';

/** This action is documented in includes/class-wp-api-search-activator.php */
//register_activation_hook( __FILE__, array( 'WP_API_Search_Activator', 'activate' ) );
if( !class_exists( 'WP_API_Search_Activator' ) ) {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-api-search-activator.php';
}

if( class_exists( 'WP_API_Search_Activator' ) ) {
  $activate = new WP_API_Search_Activator();
  // Register for activation
  register_activation_hook( __FILE__, array( &$activate, 'activate' ));
  	//register_activation_hook( __FILE__, array( 'WP_API_Search_Activator', 'activate' ) );
}

/** This action is documented in includes/class-plugin-name-deactivator.php */
register_deactivation_hook( __FILE__, array( 'WP_API_Search_Deactivator', 'deactivate' ) );


/**
 * Custom widget display for the search form
 * @since 		0.0.1
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-api-search-widget.php'; 

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-api-search.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_api_search() {

	$plugin = new WP_API_Search();
	$plugin->run();

}
run_wp_api_search();

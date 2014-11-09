<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP API Search
 * @subpackage wp-api-search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WP API Search
 * @subpackage wp-api-search/public
 * @author     Corey Burns <coreyaburns@gmail.com>
 */
class WP_API_Search_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// update shortcode properly
		add_shortcode('wp-api-search-results', array($this, 'output_results'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script('typeahead', plugin_dir_url( __FILE__ ) . 'js/typeahead.js', array( 'jquery' ), '', false);
		$options_arr = $this->options_arr();
		wp_localize_script('typeahead', 'wp_api_search_vars', $options_arr);

		wp_enqueue_script('wp-api-search-lookup', plugin_dir_url( __FILE__ ) . 'js/wp-api-search-lookup.js', array( 'jquery' ), $this->version, true);

		
	}


	public function cancel_query( $query ) {

	    if ( !is_admin() && !is_feed() && is_search() ) {
	        $query = false;
	    }
	 
	    return $query;
	}

	private function options_arr() {
		$options_arr = array(
			'google_api_key',
			'google_search_engine_id',
			'wp_api_search_post_types',
			'posts_per_page',
		);

		$options_assc_arr = array();
		foreach($options_arr as $k=>$v) {
			$options_assc_arr[$v] = get_option($v);
		}

		return $options_assc_arr;
	}
 

 	public function my_search_excerpt( $content ) {
    if ( is_search() ) {
        $content = 'This is a search excerpt for ' . get_the_title();
        // maybe add a read more link
        // also, you can use global $post to access the current search result
    }
   //return test($content);
	}


	public function output_results() {
		wp_enqueue_script( 'wp-api-search-page-lookup', plugin_dir_url( __FILE__ ) . 'js/wp-api-search-page-lookup.js', array( 'jquery' ), $this->version, true );
		
		// Possibly move options_arr to wp-api-search-lookup
		$wp_options_arr = array('posts_per_page' => get_option('posts_per_page'));
		
		wp_localize_script('wp-api-search-page-lookup', 'wp_options', $wp_options_arr);

    return '<section id="wp-api-search-results">Loading Results</section>';
	}
}

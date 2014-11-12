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

		$options_arr['ajaxurl'] = admin_url( 'admin-ajax.php' );
		//$options_arr['nonce'] = wp_create_nonce( "save_search_term_nonce" );
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
			'common_words_ignored',
		);

		$options_assc_arr = array();
		foreach($options_arr as $k=>$v) {
			$options_assc_arr[$v] = get_option($v);
		}

		$options_assc_arr['site_url'] = get_site_url();
		return $options_assc_arr;
	}
 

	public function save_search_term() {
		$results = wp_kses(isset($_POST['results']), array());
		if(!$results) {
			// if results is false go get the suggested pages to return.
			$suggested_pages = get_option('suggested_posts');


			$args = array( 
				'post__in' => $suggested_pages, 
				'post_type' => get_option('wp_api_search_post_types'),
				'posts_per_page' => -1, 
				'orderby' => 'post_type',
				'nopaging' => true,
			);
			$suggested = new WP_Query($args);

			$suggested_output = array();
			$lastPostType = '';
			$output = '';

			// The Suggested Loop
			while ( $suggested->have_posts() ) {
				$suggested->the_post();
				$id = get_the_ID();
				$title = get_the_title();
				$img = get_the_post_thumbnail($id, 'thumbnail');
				$link = get_permalink($id);
				$excerpt = get_the_excerpt();
				$postType = get_post_type($id);


				if($postType !== $lastPostType) {
					if($lastPostType !== '') {
						$output .= '</section>';
					}
					$output .= '<section>';
					$output .= '<h1>Suggested ' . $postType .'s</h1>';
				}

				$output .= '<article>';
				if($img) {
					$output .= $img;
				}
				$output .= '<h2 style="clear: none;"><a href="' . $link . '">' . $title . '</a></h2>';
				$output .= $excerpt;
				$output .= '</article>';
				

				$lastPostType = $postType;
			}

			$output .= '</section>';
			$suggested_output[] = $output;
			wp_send_json_success( $suggested_output );
		} else {
			// save the search term 
			//$full_search_term = wp_kses($_POST['full_search'], array());
			//$search_term = wp_kses($_POST['term'], array());
			//echo json_encode('results!');
		}
		
	}

	public function output_results() {

		wp_enqueue_script( 'wp-api-search-page-lookup', plugin_dir_url( __FILE__ ) . 'js/wp-api-search-page-lookup.js', array( 'jquery' ), $this->version, true );

    return '<section id="wp-api-search-results"><h1>Search Results for <span></span></h1></section><button id="wp-api-search-more">Load More</button>';
	}
}

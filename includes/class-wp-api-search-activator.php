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

	public function __construct() {
		$this->ignore_common_words();
		$this->activate();
	}
	/**
	 * Common words to be ignored
	 *
	 * Sets up option for common words to be ignored.
	 *
	 * @since    0.0.1
	 */
	public function activate() {
		
		add_action( 'init', array($this, 'setup_wp_api_search_post_type') );
		//add_action( 'init', array($this, 'wp_api_search_taxonomy_init') );
		add_shortcode( 'test', array($this, 'search_results_shortcode'));
	}

	private function ignore_common_words() {
		// function common_words returns array of words
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/func-initial-common-words.php';

		$common_words = common_words();

		add_option('common_words_ignored', $common_words);
		// add_option does not overwrite if this plugin has been enabled previously.
	}

	public function setup_wp_api_search_post_type() {
		// SETUP CUSTOM POST TYPE and COLUMNS BELOW 
	  $labels = array(
	    'name'               => __( 'WP API Search Terms', 'post type general name'),
	    'singular_name'      => __( 'WP API Search Term',  'post type singular name'),
	    'add_new'            => __( 'Add New', 'wp api search term' ),
	    'add_new_item'       => __( 'Add New Search Term' ),
	    'edit_item'          => __( 'Edit Search Term' ),
	    'new_item'           => __( 'New Search Term' ),
	    'all_items'          => __( 'All Search Terms' ),
	    'view_item'          => __( 'View Search Terms' ),
	    'search_items'       => __( 'Search Terms' ),
	    'parent_item_colon'  => '',
	    'menu_name'          => 'WP API Search Term'
	  );
	  $args = array(
	    'labels'        => $labels,
	    'description'   => 'WP API Search Terms',
	    'public'        => true,
	    'exclude_from_search' => false,
	    'show_ui'				=> true,
	    'menu_position' => 100,
	    'supports'      => array('title', 'custom fields', 'page-attributes'),
	  );
	  register_post_type( 'wp-api-search-term', $args ); 
	}

	public function wp_api_search_taxonomy_init() {
		$labels = array(
			'label' => __( 'Popularity' ),
		);
		$labels = array(
			'name'              => _x( 'Popularity', 'taxonomy general name' ),
			'singular_name'     => _x( 'Popularity', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Popularity' ),
			'all_items'         => __( 'All Popularity' ),
			'parent_item'       => __( 'Parent Popularity' ),
			'parent_item_colon' => __( 'Parent Popularity:' ),
			'edit_item'         => __( 'Edit Popularity' ),
			'update_item'       => __( 'Update Popularity' ),
			'add_new_item'      => __( 'Add New Popularity' ),
			'new_item_name'     => __( 'New Popularity Name' ),
			'menu_name'         => __( 'Popularity' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'popularity' ),
		);
		// create a new taxonomy
		register_taxonomy( 'popularity', 'wp-api-search-term', $args );
		register_taxonomy_for_object_type( 'popularity', 'wp-api-search-term' );
		add_filter( 'manage_taxonomies_for_wp_api_search_term_columns', 'wp-api-search-term_type_columns' );
	}


	function wp_api_search_term_type_columns( $taxonomies ) {
	    $taxonomies[] = 'popularity';
	    return $taxonomies;
	}
}

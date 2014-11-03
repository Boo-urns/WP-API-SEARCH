<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP API Search
 * @subpackage wp-api-search/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WP API Search
 * @subpackage wp-api-search/admin
 * @author     Corey Burns <coreyaburns@gmail.com>
 */
class WP_API_Search_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings option array (labels) for this plugin
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      array    $settings_arr    he setting options array for the plugin.
	 */
	private $settings_arr;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $settings_arr ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings_arr = $settings_arr;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Settings options page creation
	 *
	 * @since 0.0.1
	 */
	public function create_setting_options_page() {

		add_options_page('WP API Search', 'WP API Search', 'administrator', __FILE__, array($this, 'build_setting_options_page'));

	}

	/** 
	  * Build setting page 
	  *
	  * Frontend for the settings page
	  *
	  * @since 0.0.1
	  */
	public function build_setting_options_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wp-api-search-options-page-display.php';

	}

	/**
		* Register and build setting fields
		*
		* TODO Extend the foreach to look for proper labels for extensible in the future
		*
		* @since 0.0.1
		*/
	public function register_and_build_setting_fields() {

		add_settings_section('main_section', '', array($this, 'section_cb'), 'wpapisearch');
		// add_settings_section - id, title, callback, page

		// The settings labels (id, arg is created from these)
		// TO DO
		foreach($this->settings_arr as $k=>$v){ 
				foreach($this->settings_arr->$k as $id => $val) {
						add_settings_field(
							$id, 						// setting option id
							$val,  						// label
							array( $this, 'setting_fields' ), // callback
							'wpapisearch', 	// page
							'main_section',	// section
							array( $id )	 	// the $args
						);

						register_setting(
								'wpapisearch', // option group
								$id,					 // option name
								array( $this, 'validate_setting' ) // sanitize callback
						);
				}
		}

	}


	public function setting_fields($args) {

			$option = get_option($args[0]);  
			echo "<input name='$args[0]' type='text' value='$option' />";
	}

	public function validate_setting($plugin_options) {  
		return $plugin_options;
	}

	// necessary to avoid throwing an error from add_settings_section
	public function section_cb(){ }
}

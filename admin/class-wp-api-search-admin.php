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
	 * @var 		 array 		 $settings_arr		The setting options 
	 * @var 		 string 	 $common_words 		Common words ignored option label.
	 */
	public function __construct( $plugin_name, $version, $settings_arr ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings_arr = $settings_arr;
		$this->common_words = 'common_words_ignored';

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

		//add_options_page('WP API Search', 'WP API Search', 'administrator', __FILE__, array($this, 'build_setting_options_page'));
		//add_action(‘admin_menu’ , ‘brdesign_enable_pages’);
		add_submenu_page('edit.php?post_type=wp-api-search-term', 'WP API Search Admin', 'Settings', 'edit_posts', basename(__FILE__), array($this, 'build_setting_options_page'));

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


		// GET COMMON WORD LIST
		// $word_list = get_option($this->common_words);

	}

	/**
		* Register and build setting fields
		*
		* TODO Extend the foreach to look for proper labels for extensible in the future
		* Possibly move page and section parameters for add_settings_field, register_setting to $this->settings_arr on class-wp-api-search.php
		*
		* @since 0.0.1
		*/
	public function register_and_build_setting_fields() {

		add_settings_section('main_section', '', array($this, 'section_cb'), 'wpapisearch');
		add_settings_section('suggested_section', '', array($this, 'section_cb'), 'suggested');

		// add_settings_section - id, title, callback, page

		// The settings labels (id, arg is created from these)
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

		// Suggested page suggested post settings
		add_settings_field(
				'suggested_posts',
				'Suggested Pages',
				array( $this, 'suggested_fields' ),
				'suggested',
				'suggested_section',
				array( 'suggested_posts' )
		);

		register_setting(
			'suggested',
			'suggested_posts',
			array( $this, 'validate_setting' )
		);


		// Common Words Ignored
		$label = ucwords(implode(' ', explode('_', $this->common_words)));
		add_settings_field(
			$this->common_words,
			$label,
			array( $this, 'common_words_textarea'),
			'wpapisearch',
			'main_section',
			array( $this->common_words )
		);

		register_setting(
			'wpapisearch',
			$this->common_words,
			array( $this, 'validate_textarea_to_arr' )
		);
	}

	public function setting_fields($args) {

		$option = get_option($args[0]);  
		echo "<input name='$args[0]' type='text' value='$option' />";
	}

	public function suggested_fields($args) {

		$post_types = get_option('wp_api_search_post_types');
		$option = (array) get_option($args[0]); 

		foreach($post_types as $type) {
			echo '<section class="suggested_posts"><h3>' . strtoupper($type) . '</h3>';
			echo '<ul style="-webkit-column-count: 2;">';
			$type_query = new WP_Query( array( 
				'post_type' => $type,
				'orderby' => 'title',
				'order'		=> 'ASC',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				)
			);

			while($type_query->have_posts() ) {
				$type_query->the_post();
				$title = get_the_title();
				$id = get_the_ID();
			?>

			<li>
				<input type="checkbox" id="<?php echo $title; ?>" name="<?php echo $args[0]; ?>[]" value="<?php echo $id; ?>" <?php if(in_array($id, $option)) { echo "checked"; } ?>>
				<label for="<?php echo $title; ?>"><?php echo $title; ?></label>
			</li>
			
			<?php
			}

			echo '</ul></section>';
			wp_reset_postdata();
		}

	}

	public function common_words_textarea($args) {
		$option = (array) get_option($args[0]);
		$option_str = '';


		foreach($option as $val) {
			$option_str .= $val . ', ';
		}
		
		$option_str = substr($option_str, 0, -2);

		echo "<textarea name='$args[0]' style='width: 80%; height: 250px;'>$option_str</textarea>";
	}
		/**
		* Register and build setting post type fields
		*
		* @since 0.0.1
		*/
	public function register_and_build_setting_post_type_fields() {

		// The settings labels (id, arg is created from these)
		$id = 'wp_api_search_post_types';
		$val = 'Searchable Post Types';

		add_settings_field(
			$id, 						// setting option id
			$val,  						// label
			array( $this, 'setting_post_type_fields' ), // callback
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

	public function setting_post_type_fields($args) {
		$post_type_args = array(
    	'public'   => true,
		);
		$output = 'names'; // names or objects, note names is the default
		$operator = 'or'; // 'and' or 'or'

		$post_types = get_post_types( $post_type_args, $output, $operator ); 

		$option = (array) get_option($args[0]);

		foreach ( $post_types  as $post_type ) { ?>

			<input type="checkbox" id="<?php echo $post_type; ?>" name="<?php echo $args[0]; ?>[]" value="<?php echo $post_type; ?>" <?php if(in_array($post_type, $option)) { echo "checked"; } ?>>
			<label for="<?php echo $post_type; ?>"><?php echo $post_type; ?></label><br>
		
		<?php
		}

	}

	public function validate_textarea_to_arr($options) {
		$options = explode(', ', $options);
		return $options;
	}

	public function validate_setting($options) { 
		return $options;
	}

	// necessary to avoid throwing an error from add_settings_section
	public function section_cb(){ }
}

<?php
/**
 * The file that defines the plugin's widget class
 *
 * A class definition that includes custom widget display of search box and autosuggest
 *
 * @since      0.0.1
 *
 * @package    WP API Search
 * @subpackage WP_API_Search/includes
 */
// Creating the widget 
class wp_api_search_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'wp_api_search_widget', 

		// Widget name will appear in UI
		__('WP API Search', 'wp_api_search_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Search box with autosuggest utilizing WP API', 'wp_api_search_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$placeholder = apply_filters( 'widget_title', $instance['placeholder'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		$siteURL = get_site_url();
		echo "<form id='wp_api_search' action='$siteURL' method='GET'><label for='wp_api_search_widget'>Search</label>
					<input type='text' id='wp_api_search_input' name='s' placeholder='$placeholder'>
					<input type='text' id='wp_api_search_spelling_suggestion' name='wp_api_search_spelling_suggestion' disabled>
					<input type='hidden' id='full_search' name='full_search'>
					<input type='submit' id='wp_api_search_submit' value='Search'></form>";

		// This is where you run the code and display the output
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'placeholder' ] ) ) {
		$placeholder = $instance[ 'placeholder' ];
		}
		else {
		$placeholder = __( 'Search by keyword', 'wp_api_search_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'placeholder' ); ?>"><?php _e( 'Placeholder:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'placeholder' ); ?>" name="<?php echo $this->get_field_name( 'placeholder' ); ?>" type="text" value="<?php echo esc_attr( $placeholder ); ?>" />
		</p>
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['placeholder'] = ( ! empty( $new_instance['placeholder'] ) ) ? strip_tags( $new_instance['placeholder'] ) : '';
		return $instance;
	}
} // Class wp_api_search_widget ends here

// Register and load the widget
function wp_api_load_widget() {
	register_widget( 'wp_api_search_widget' );
}
add_action( 'widgets_init', 'wp_api_load_widget' );
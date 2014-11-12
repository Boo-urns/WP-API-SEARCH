<?php

/**
 * Provide a settings page
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    WP API Search
 * @subpackage wp-api-search/admin/partials
 */
?>

<div id="wrap">
<h2>WP API Search Options</h2>    
<p>Setup options below..</p>
<?php
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_options';
?>
<h2 class="nav-tab-wrapper">

    <a href="?post_type=wp-api-search-term&page=class-wp-api-search-admin.php&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>">General</a>
    <a href="?post_type=wp-api-search-term&page=class-wp-api-search-admin.php&tab=suggested_pages_option" class="nav-tab <?php echo $active_tab == 'suggested_pages_option' ? 'nav-tab-active' : ''; ?>">Suggested Pages</a>
</h2>
<form method="post" action="options.php" enctype="multipart/form-data">  
	<?php
		if( $active_tab == 'general_options' ) {
			settings_fields( 'wpapisearch' );
			do_settings_sections( 'wpapisearch' ); 
		} else {
			echo "";
			settings_fields( 'suggested' );
			do_settings_sections( 'suggested' );
		}
	?>  

	<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></p>
</form>
</div>
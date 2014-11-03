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
<p>Setup options below.</p>    
<form method="post" action="options.php" enctype="multipart/form-data">  
	<?php //settings_fields('plugin_options'); ?>  
	<?php //do_settings_sections('wpapisearch'); ?>  

	<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></p>
</form>
</div>
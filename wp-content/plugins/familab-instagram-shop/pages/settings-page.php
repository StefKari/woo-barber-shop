<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
Familab_Instagram_Shop::plugin_check();

$tis_ins_token = get_option('tis_ins_token');
?>
<div class="wrap">
    <h1 class="wp-heading-inline"> 
    	<?php esc_html_e('Familab Instagram Shop Configuration','familab-instagram-shop') ?>
    </h1>
    <hr class="wp-header-end">
    <form class="tis-instagram-token-form" method="post" action="options.php">
   		 <?php settings_fields( 'tis-settings-group' ); ?>
    	<label>
    		<?php esc_html_e( 'Instagram Access token ', 'familab-instagram-shop' ) ?> 
    		<input type="text" name="tis_ins_token" value="<?php echo esc_attr($tis_ins_token); ?>">
    		<a target="_blank" class="row-title" href="http://instagram.pixelunion.net/">
    			<?php esc_html_e("Don't know your Instagram Access Token? Click here to get",'familab-instagram-shop') ?>
    		</a>
    	</label>
    	<?php Tis_Functions::token_validate(); ?>
    	<?php submit_button(); ?>
    </form>
</div>
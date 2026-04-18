<?php 
/**
 * The Template for displaying back end edit page
 */

defined( 'ABSPATH' ) || exit;

global $post, $tis;
$tis_ins_token = get_option('tis_ins_token');
$pin_data_str    = get_post_meta( $post->ID, 'tis_pin_data', true );
$pin_data_str = is_serialized( $pin_data_str ) ? unserialize($pin_data_str) : $pin_data_str ;
$pin_data_array = json_decode( $pin_data_str );
$has_image = false;   
$warning_class = '';    
if ( trim( $pin_data_str ) != '' ) {
	$has_image = true;
	$warning_class = 'display:none;';
}else{
	$has_image = false;
}

?>
<div class="tis-content-wrapper clearfix">
	<div class="modal fade" id="tis-edit-modal" role="dialog" aria-hidden="true" is-editing="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
			<?php
			Tis_Edit_Page::modal_edit();						    
			?>
		    </div>
	  	</div>
	</div>
	<div id="tis-main-content" >
    	<?php wp_nonce_field( 'tis_edit_nonce', 'tis_edit_nonce' ); ?>
        <div class="tis-wrapper">
            <div class="tis-top">
                <div class="title mb-4">
                    <input type="text" name="post_title" class="input-text post-title"
                           placeholder="<?php esc_html_e( 'Enter Title Here', 'familab-instagram-shop' ); ?>"
                           value="<?php echo esc_attr( $post->post_title ); ?>">
                </div>
            </div>
            <div class="tis-main">
            	<div class="instagram-images-overlay">
				</div>
            	<div class="tis-drawer">
					<div class="tis-tool-bar">
						  <div class="tis-toggle-picker btn btn-danger tis-tool-bar-btn" title="<?php esc_attr_e('Select images', 'familab-instagram-shop'); ?>"><i class="fa fa-camera"></i></div>
						  <div class="tis-toggle-settings btn tis-tool-bar-btn btn-light" title="<?php esc_attr_e('Shortcode Settings', 'familab-instagram-shop'); ?>"><i class="fa fa-cog"></i></div>

						  <div data-current-view="col-4" class=" tis-toggle-view tis-tool-bar-btn btn btn-light" title="<?php esc_attr_e('Switch view', 'familab-instagram-shop'); ?>" ><i class="fa fa-search"></i></div>
						  <div class="tis-refresh-images btn tis-tool-bar-btn btn-light" title="<?php esc_attr_e('Refresh Images (This will fix issue with some images not displayed)', 'familab-instagram-shop'); ?>"><i class="fa fa-refresh"></i></div>
					</div>
					<?php
					Tis_Edit_Page::drawer_tab_images($tis_ins_token);

					/**
					 * Applied Filter: tis_layout_settings
					 * Applied Filter: tis_type_settings
					 * @param   array $tis_layout
					 * @param   array $tis_type
					 * @return  array
	      			*/
					Tis_Edit_Page::drawer_tab_settings();


					
					?>
            	</div>
            	<div class="tis-working-area">
            		<div class="tis-js-choosen-images">
						<div class="tis-used-wrap">
						    <input  type="hidden" name="social_shop_pin" id="tis-metafields" value="<?php echo esc_attr( wp_json_encode( $pin_data_array ) ); ?>">
						    <div class="row tis-image-items tis-sortable">
						    	<?php 
						    	if($has_image){
						        	foreach ( $pin_data_array as $image_item=>$value ) {
						        		Tis_Edit_Page::single_image_loop($image_item, $value);
						        	}
						    	} 
						    	?>
						    </div>
					    	<div class="tis-error-field no-image p-3 mb-2 bg-warning text-dark" style="<?php echo esc_attr($warning_class); ?>">
					    		<?php esc_html_e( 'No image chosen', 'familab-instagram-shop' ); ?>
					    	</div>
						</div>
            		</div>
            	</div>
            </div>
        </div>
    </div>	
</div>

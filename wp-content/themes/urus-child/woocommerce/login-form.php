<div id="urus-login-form-popup" class="urus-login-form urus-login-form-popup mfp-hide">
    <form id="login" action="login" method="post">
        <div class="form-head">
            <?php urus_Helper::get_logo();?>
        </div>
        <p class="form-login-message">
            <?php esc_html_e('Great to have you back!','urus');?>
        </p>
        <div class="status"></div>
        <p>
            <input placeholder="<?php esc_attr_e('Username','urus');?>" class="input-text" id="username" type="text" name="username">
        </p>
        <p>
            <input class="input-text" id="password" type="password" name="password" placeholder="<?php esc_attr_e('Password','urus');?>">
        </p>
        <p>
            <a class="lost" href="<?php echo wp_lostpassword_url(); ?>"><?php esc_html_e('Lost your password?','urus');?></a>
        </p>
        <input class="submit_button" type="submit" value="<?php esc_attr_e('Sign In to Your Account','urus');?>" name="submit">
        
        
        <?php wp_nonce_field( 'urus-ajax-login-nonce', 'security' ); ?>
        <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) :
            
            $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
            $myaccount_link = get_permalink( get_option('woocommerce_myaccount_page_id') );
            ?>
            
            <div class="register__message"> <?php esc_html_e('Don’t have an account?','urus');?> <a href="<?php echo esc_url($myaccount_link);?>"><?php esc_html_e('Sign up now →','urus');?></a></div>
        <?php endif;?>
    </form>
</div>
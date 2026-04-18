<?php
if( !class_exists('Urus_Mailchimp')){
    class Urus_Mailchimp{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        /**
         * Initialize pluggable functions.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            add_action( 'wp_ajax_submit_mailchimp_via_ajax', array( __CLASS__, 'submit_mailchimp_via_ajax' ) );

            add_action( 'wp_ajax_nopriv_submit_mailchimp_via_ajax', array( __CLASS__, 'submit_mailchimp_via_ajax' ) );
            add_action('wp_footer',array(__CLASS__,'newsletter_form_sticky'),10);
            add_action( 'body_class', array( __CLASS__, 'body_class' ) );
            // State that initialization completed.
            self::$initialized = true;
        }
        public static function submit_mailchimp_via_ajax(){
            if ( !class_exists( 'MCAPI' ) ) {
                get_template_part( 'libraries/MCAPI.class' );
            }
            $response        = array(
                'html'    => '',
                'message' => '',
                'success' => 'no',
            );
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $api_key = Urus_Helper::get_option('mailchimp_api_key', '');
            $list_id = Urus_Helper::get_option('mailchimp_list_id', '');
            $success_message = Urus_Helper::get_option('mailchimp_success_message','Thanks for Subscribe!');
            $response['message'] = esc_html__( 'Failed', 'urus' );
            if( $email ==''){
                $response['message'] = esc_html__( 'Please enter your email address', 'urus' );
                wp_send_json( $response );
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = esc_html__( 'Invalid Email Format', 'urus' );
                wp_send_json( $response );
            }
            $merge_vars          = array();
            if ( class_exists( 'MCAPI' ) ) {
                $api = new MCAPI( $api_key );
                if ( $api->subscribe( $list_id, $email, $merge_vars ) === true ) {
                    $response['message'] = sanitize_text_field( $success_message );
                    $response['success'] = 'yes';
                } else {
                    // Sending failed
                    $response['message'] = $api->get_reposive_message();
                }
            }
            wp_send_json( $response );
            die();
        }
        public static function body_class( $classes){
            $used_newsletter_form_sticky = Urus_Helper::get_option('used_newsletter_form_sticky',0);
            if( $used_newsletter_form_sticky == 1){
                $classes[] ='is-newsletter-stiky';
            }
            return $classes;
        }
        public static function newsletter_form_sticky(){
            $used_newsletter_form_sticky = Urus_Helper::get_option('used_newsletter_form_sticky',0);
            if( $used_newsletter_form_sticky == 0 ) return;
            ?>
            <div class="envi-newsletter-form-sticky">
                <div class="container">
                    <div class="urus-newsletter-form">
                        <h3 class="title"><?php esc_html_e('Subscribe our newsletter and get 15% Off first buy','urus');?></h3>
                        <div class="content">
                            <div class="content-inner ">
                                <input type="email" name="email" class="form-field" placeholder="<?php esc_attr_e('Your email address','urus');?>">
                                <button class="newsletter-form-button"><?php esc_html_e('Subscribe','urus');?></button>
                            </div>
                        </div>
                    </div>
                    <a class="close-newster-sticky" href="#"><span class="urus-icon urus-icon-close2"></span></a>
                </div>
            </div>
            <?php
        }
    }
}
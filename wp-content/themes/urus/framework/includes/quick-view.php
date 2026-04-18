<?php
if( !class_exists('Urus_Quick_View')){
    class Urus_Quick_View{
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
            add_filter('urus_settings_section_woocommerce',array(__CLASS__,'settings'),101,1);
            $enable_quick_view = Urus_Helper::get_option('enable_quick_view',0);

            if( $enable_quick_view == 0  || Urus_Mobile_Detect::isMobile() ){
                return;
            }


            add_action( 'wp_ajax_urus_quick_view', array(__CLASS__,'display') );
            add_action( 'wp_ajax_nopriv_urus_quick_view', array(__CLASS__,'display') );

            add_action( 'urus_function_shop_loop_item_quickview', array( __CLASS__, 'button' ), 10 );

            add_action('wp_footer',array(__CLASS__,'create_div'));

            add_action( 'urus_single_product_summary', 'woocommerce_template_single_rating', 4 );
            add_action('urus_single_product_summary','woocommerce_template_single_title',5);
            add_action('urus_single_product_summary','woocommerce_template_single_price',10);
            add_action('urus_single_product_summary','woocommerce_template_single_excerpt',20);
            add_action( 'urus_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            self::$initialized = true;
        }

        public static function settings($settings){
            $settings[] = array(
                'title'        => esc_html__( 'Quick View', 'urus' ),
                'subsection'   => true,
                'fields'       => array(
                    array(
                        'id'       => 'enable_quick_view',
                        'type'     => 'switch',
                        'title'    => esc_html__('Enable Quick View','urus'),
                        'default'  => true
                    ),
                    array(
                        'id'       => 'quickview_layout',
                        'type'     => 'select',
                        'title'    => esc_html__('Quick View Layout', 'urus'),
                        'subtitle' => esc_html__('Select Quick View layout', 'urus'),
                        'options'  => apply_filters('urus_settings_quickview_layout',
                            array(
                                'default' => esc_html__('Popup','urus'),
                                'quickview-style-02' => esc_html__('Drawer','urus')
                            )
                        ),
                        'default'  => 'default',
                    ),
                )
            );
            return $settings;
        }

        public static function button(){
            global $product;
            if (is_null($product)){
                return;
            }
            $nonce = wp_create_nonce( 'urus_quick_view' );

            $hint_class = Urus_Pluggable_WooCommerce::get_product_loop_hint_class('quick_view');
            ?>
            <div class="quick-view-btn <?php echo esc_attr($hint_class);?>" aria-label="<?php echo esc_attr__('Quick View','urus');?>" data-pid="<?php echo esc_attr($product->get_id());?>" data-nonce="<?php echo esc_attr($nonce);?>">
                <a href="#" aria-label="<?php echo esc_attr__('Quick View','urus');?>" rel="nofollow"><?php esc_html_e('Quick View','urus');?></a>
            </div>
            <?php
        }

        public static function display(){
            $productid = isset($_POST['productId']) ? $_POST['productId'] :0;
            if( $productid == 0) return;
            $args['post_type'] = 'product';
            $args['post_status']         = 'publish';
            $args['ignore_sticky_posts'] = 1;
            $args['suppress_filter']     = true;
            $args['posts_per_page'] = -1;
            $args['post__in'] = array_map( 'trim', explode( ',', $productid ) );
            $args['orderby']  = 'post__in';
            $urus_products = new WP_Query( $args);
            $class[] ='product';
            ?>
            <a class="quick-view-close" href="#"><?php esc_html_e('Close','urus'); ?></a>
            <?php if($urus_products->have_posts()): ?>
                <?php while ( $urus_products->have_posts() ) : $urus_products->the_post(); ?>
                    <div id="product-<?php the_ID(); ?>" <?php wc_product_class($class); ?>>
                        <div class="urus-single-product-top clearfix">
                            <div class="urus-product-gallery__wrapper clearfix nav-center">
                                <?php
                                    global  $product;
                                    $atts = array(
                                        'loop'         => 'true',
                                        'ts_items'     => 1,
                                        'xs_items'     => 1,
                                        'sm_items'     => 1,
                                        'md_items'     => 1,
                                        'lg_items'     => 1,
                                        'ls_items'     => 1,
                                        'navigation'   => 'true',
                                        'slide_margin' => 0,
                                        'dots' => 'false'
                                    );
                                    $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
                                    $post_thumbnail_id = $product->get_image_id();
                                    $attachment_ids = $product->get_gallery_image_ids();

                                    if( has_post_thumbnail() ){
                                        array_unshift($attachment_ids,$post_thumbnail_id);
                                    }
                                    if( !empty($attachment_ids)){
                                        ?>
                                        <div class="images swiper-container urus-swiper" <?php echo esc_attr($carousel_settings)?>>
                                            <div class="swiper-wrapper">
                                                <?php foreach ($attachment_ids as $attachment_id):?>
                                                    <div class="swiper-slide">
                                                        <?php echo wc_get_gallery_image_html($attachment_id);?>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                            <!-- If we need pagination -->
                                            <div class="swiper-pagination"></div>
                                        </div>
                                        <!-- If we need navigation buttons -->
                                        <div class="slick-arrow next">
                                            <?php echo familab_icons('arrow-right3'); ?>
                                        </div>
                                        <div class="slick-arrow prev">
                                            <?php echo familab_icons('arrow-left3'); ?>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </div>
                            <div  class="summary entry-summary clearfix">
                                <div id="summary" >
                                    <div class="summary__inner__wapper">
                                        <div class="summary__inner clearfix">
                                            <?php
                                                /**
                                                 * Hook: urus_single_product_summary.
                                                 *
                                                 * @hooked woocommerce_template_single_title - 5
                                                 * @hooked woocommerce_template_single_rating - 10
                                                 * @hooked woocommerce_template_single_price - 10
                                                 * @hooked woocommerce_template_single_excerpt - 20
                                                 * @hooked woocommerce_template_single_add_to_cart - 30
                                                 */
                                                do_action( 'urus_single_product_summary' );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;?>
            <?php endif;?>
            <?php
            wp_reset_postdata();
            wp_die();
        }
        public static function create_div(){
            $quickview_layout = Urus_Helper::get_option('quickview_layout');
            ?>
            <script type="text/template" id="tmpl-quickview-variation-template">
                <div class="woocommerce-variation-description">{{{ data.variation.variation_description }}}</div>
                <div class="woocommerce-variation-price">{{{ data.variation.price_html }}}</div>
                <div class="woocommerce-variation-availability">{{{ data.variation.availability_html }}}</div>
            </script>
            <script type="text/template" id="tmpl-quickview-unavailable-variation-template">
                <p><?php esc_html_e( 'Sorry, this product is unavailable. Please choose a different combination.', 'urus' ); ?></p>
            </script>
            <?php
            if ($quickview_layout == 'quickview-style-02') {
                ?>
                <div id="urus-quickview" class="drawer quickview-drawer quickview-style-02">
                    <a class="quick-view-close" href="#"><?php esc_html_e('Close','urus');?></a>
                    <div class="quickview-product-wrapper">
                        <div class="urus-single-product-top clearfix">
                            <div class="urus-product-gallery__wrapper clearfix">
                                <div class="images">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }else{
                ?>
                <div id="urus-quickview">
                    <a class="quick-view-close" href="#"><?php esc_html_e('Close','urus');?></a>
                    <div class="quickview-product-wrapper">
                        <div class="urus-single-product-top clearfix">
                            <div class="urus-product-gallery__wrapper clearfix">
                                <div class="images">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

        }
    }
}

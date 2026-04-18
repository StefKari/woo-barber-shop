<?php
if(!class_exists('Urus_Shortcodes_Product_Deal')){
    class Urus_Shortcodes_Product_Deal extends Urus_Shortcodes{
        public $shortcode = 'product_deal';

        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_special_banner', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_special_banner_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_product_deal', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus_product_deal' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_product_deal', $atts );
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( isset($atts['product_deal_image']) && $atts['product_deal_image'] > 0):?>
                    <?php
                    $image = Urus_Helper::resize_image($atts['product_deal_image'],false,false,true,true);
                    ?>
                    <div class="deal-image">
                        <figure>
                            <?php echo Urus_Helper::escaped_html($image['img']);?>
                        </figure>
                    </div>
                <?php endif;?>
                <div class="content">
                    <?php if( isset($atts['id']) && $atts['id'] >0 ):?>
                        <?php
                        $_product = wc_get_product( $atts['id']);
                        ?>
                        <?php if($_product):?>
                            <?php
                            $date = Urus_Pluggable_WooCommerce::get_max_date_sale($_product);
                            ?>
                            <?php if( $atts['layout']=='layout1'):?>
                                <div class="block-left">
                                    <h3 class="block-title"><?php echo esc_html($atts['title']);?></h3>
                                    <div class="product">
                                        <h3 class="product-name">
                                            <a href="<?php echo esc_url(get_permalink($_product->get_id()));?>"><?php echo esc_html($_product->get_title());?></a>
                                        </h3>
                                        <span class="price">
                                    <?php echo Urus_Helper::escaped_html($_product->get_price_html());?>
                                     </span>
                                        <?php if ( $_product->is_featured() ) : ?>
                                            <?php echo apply_filters( 'woocommerce_featured_flash', '<span class="featured-flash"><span class="text">' . esc_html__( 'Hot', 'urus' ) . '</span></span>', $_product ); ?>
                                        <?php endif; ?>

                                        <a class="button" href="<?php echo esc_url(get_permalink($_product->get_id()));?>"><?php esc_html_e('Shop now','urus');?></a>
                                    </div>
                                </div>
                                <div class="block-right">
                                    <?php if($atts['product_image']):?>
                                        <div class="product-image">
                                            <?php
                                                $image = Urus_Helper::resize_image($atts['product_image'],false,false,true,true);
                                            ?>
                                            <figure>
                                                <?php echo Urus_Helper::escaped_html($image['img']);?>
                                            </figure>
                                        </div>
                                    <?php endif;?>
                                    <?php if( $date > 0):?>
                                        <div class="urus-countdown product-deal-countdown" data-datetime="<?php echo date( 'm/j/Y g:i:s', $date); ?>"></div>
                                    <?php endif;?>
                                </div>
                            <?php else:?>
                                <div class="product">
                                    <h3 class="product-name">
                                        <a href="<?php echo esc_url(get_permalink($_product->get_id()));?>"><?php echo esc_html($_product->get_title());?></a>
                                    </h3>
                                    <span class="price">
                                    <?php echo Urus_Helper::escaped_html($_product->get_price_html());?>
                               </span>
                                    <?php if ( $_product->is_featured() ) : ?>
                                        <?php echo apply_filters( 'woocommerce_featured_flash', '<span class="featured-flash"><span class="text">' . esc_html__( 'Hot', 'urus' ) . '</span></span>', $_product ); ?>
                                    <?php endif; ?>
                                    <?php if( $date > 0):?>
                                        <div class="urus-countdown product-deal-countdown" data-datetime="<?php echo date( 'm/j/Y g:i:s', $date); ?>"></div>
                                    <?php endif;?>
                                    <a class="button" href="<?php echo esc_url(get_permalink($_product->get_id()));?>"><?php esc_html_e('Shop now','urus');?></a>
                                </div>
                            <?php endif;?>
                        <?php endif;?>
                    <?php endif;?>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_product_deal_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
            $params    = array(
                'base'        => 'urus_product_deal',
                'name'        => esc_html__( 'Product Deal', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Product Deal', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'param_name'  => 'layout',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' ) => 'default',
                            esc_html__( 'Layout 01', 'urus' ) => 'layout1',
                        ),
                        'std'         => 'default',
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                        'dependency'  => array(
                            'element' => 'layout',
                            'value'   => array( 'layout1'),
                        ),
                    ),
                    array(
                        "type"        => 'attach_image',
                        "heading"     => esc_html__('Image', 'urus'),
                        "param_name"  => 'product_deal_image',
                        "value"       => '',
                    ),
                    array(
                        "type"        => 'attach_image',
                        "heading"     => esc_html__('Product Image', 'urus'),
                        "param_name"  => 'product_image',
                        "value"       => '',
                        'dependency'  => array(
                            'element' => 'layout',
                            'value'   => array( 'layout1'),
                        ),
                    ),
                    array(
                        'type'        => 'autocomplete',
                        'heading'     => esc_html__( 'Product', 'urus' ),
                        'param_name'  => 'id',
                        'settings'    => array(
                            'multiple'      => false,
                            'sortable'      => true,
                            'unique_values' => true,
                        ),
                        'save_always' => true,
                        'description' => esc_html__( 'Enter Product', 'urus' ),
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}

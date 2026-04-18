<?php
 if( !class_exists('Urus_Elementor_Product_Deal')){
     class Urus_Elementor_Product_Deal extends Urus_Elementor{
         public $name ='product_deal';
         public $title ='Product Deal';
         public $icon ='eicon-countdown';
         /**
          * Register the widget controls.
          *
          * Adds different input fields to allow the user to change and customize the widget settings.
          *
          * @since 1.0.0
          *
          * @access protected
          */
         protected function _register_controls() {

             $this->start_controls_section(
                 'content_section',
                 [
                     'label' => esc_html__( 'Content', 'urus' ),
                 ]
             );
             $this->add_control(
                 'layout',
                 [
                     'label' => esc_html__( 'Layout', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'default' => esc_html__( 'Default', 'urus' ),
                         'layout1' => esc_html__( 'Layout 01', 'urus' ),
                     ],
                     'default' => 'default',
                     'label_block'=> true
                 ]
             );
             $this->add_control(
                 'title',
                 [
                     'label' => esc_html__( 'Title', 'urus' ),
                     'type' => \Elementor\Controls_Manager::TEXT,
                     'placeholder' => esc_html__( 'Enter your text', 'urus' ),
                     'label_block'=> true
                 ]
             );
             $this->add_control(
                 'product_deal_image',
                 [
                     'label' => esc_html__( 'Image', 'urus' ),
                     'type' => \Elementor\Controls_Manager::MEDIA,
                     'default' => [
                         'url' => \Elementor\Utils::get_placeholder_image_src(),
                     ]
                 ]
             );
             $this->add_control(
                 'product_image',
                 [
                     'label' => esc_html__( 'Product Image', 'urus' ),
                     'type' => \Elementor\Controls_Manager::MEDIA,
                     'default' => [
                         'url' => \Elementor\Utils::get_placeholder_image_src(),
                     ],
                     'condition' => array(
                         'layout' => 'layout1'
                     ),
                 ]
             );
             $this->add_control(
                 'id',
                 [
                     'label' => esc_html__( 'Product', 'urus' ),
                     'type' => \Elementor\Controls_Manager::NUMBER,
                     'min' => 1,
                     'step' => 1,
                     'default' => 0,
                 ]
             );
             $this->end_controls_section();

         }
         /**
          * Render the widget output on the frontend.
          *
          * Written in PHP and used to generate the final HTML.
          *
          * @since 1.0.0
          *
          * @access protected
          */
         protected function render() {
             $atts = $this->get_settings_for_display();
             $css_class    = array( 'urus_product_deal' );
             $css_class[] = $atts['layout'];
             ?>
             <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                 <?php if( isset($atts['product_deal_image']['id']) && $atts['product_deal_image']['id'] > 0):?>
                     <?php
                     $image = Urus_Helper::resize_image($atts['product_deal_image']['id'],false,false,true,true);
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
         }
     }
 }

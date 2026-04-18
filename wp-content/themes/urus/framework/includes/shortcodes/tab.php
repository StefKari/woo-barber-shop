<?php
if( !class_exists('Urus_Shortcodes_Tab')){
    class Urus_Shortcodes_Tab extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'tab';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_tab', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_tab_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_tab', $atts ) : $atts;

            extract( $atts );
            $css_class    = array( 'urus-tab ' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $css_class[]  = self::getCSSAnimation($css_animation);
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_tab', $atts );
            $sections    = self::get_all_attributes( 'vc_tta_section', $content );
            $rand        = uniqid();
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if ( $sections && is_array( $sections ) && count( $sections ) > 0 ): ?>
                    <div class="tab-head clearfix">
                        <?php
                        if ( $atts['title'] ){
                            ?>
                            <h3 class="title"><?php echo esc_html($atts['title']);?></h3>
                            <?php
                        }

                        ?>
                        <ul class="tab-link">
                            <?php foreach ( $sections as $key => $section ) : ?>
                                <?php
                                /* Get icon from section tabs */
                                $section['i_type'] = isset( $section['i_type'] ) ? $section['i_type'] : 'fontawesome';
                                $add_icon          = isset( $section['add_icon'] ) ? $section['add_icon'] : '';
                                $position_icon     = isset( $section['i_position'] ) ? $section['i_position'] : '';
                                $icon_html         = $this->constructIcon( $section );
                                $section_id        = $section['tab_id'] . '-' . $rand;
                                $class_active      = '';
                                $class_loaded      = '';
                                if ( $key == $atts['active_section'] ) {
                                    $class_active = 'active';
                                    $class_loaded = 'loaded';
                                }
                                ?>
                                <li class="<?php echo esc_attr( $class_active ); ?>">
                                    <a class="<?php echo esc_attr( $class_loaded ); ?>"
                                       data-ajax="<?php echo esc_attr( $atts['ajax_check'] ) ?>"
                                       data-animate="<?php echo esc_attr( $atts['css_animation'] ); ?>"
                                       data-section="<?php echo esc_attr( $section['tab_id'] ); ?>"
                                       data-id="<?php echo get_the_ID(); ?>"
                                       href="#<?php echo esc_attr( $section_id ); ?>">
                                        <?php if ( isset( $section['title_image'] ) ) : ?>
                                            <figure>
                                                <?php
                                                $image_thumb = apply_filters( 'urus_resize_image', $section['title_image'], false, false, true, true );
                                                echo wp_specialchars_decode( $image_thumb['img'] );
                                                ?>
                                            </figure>
                                        <?php else : ?>
                                            <?php echo ( 'true' === $add_icon && 'right' !== $position_icon ) ? $icon_html : ''; ?>
                                            <?php if (isset($section['title'])):?>
                                            <span><?php echo esc_html( $section['title'] ); ?></span>
                                            <?php endif; ?>
                                            <?php echo ( 'true' === $add_icon && 'right' === $position_icon ) ? $icon_html : ''; ?>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="tab-container">
                        <?php foreach ( $sections as $key => $section ): ?>
                            <?php
                            $section_id = $section['tab_id'] . '-' . $rand;
                            $active_tab = array( 'tab-panel' );
                            if ( $key == $atts['active_section'] )
                                $active_tab[] = 'active';
                            ?>
                            <div class="<?php echo esc_attr( implode( ' ', $active_tab ) ); ?>"
                                 id="<?php echo esc_attr( $section_id ); ?>">
                                <?php if ( $atts['ajax_check'] == '1' ) :
                                    if ( $key == $atts['active_section'] ):
                                        echo do_shortcode( $section['content'] );
                                    endif;
                                else :
                                    echo do_shortcode( $section['content'] );
                                endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return apply_filters('urus_shortcode_container_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'                    => 'urus_tab',
                'name'                    => esc_html__( 'Tabs', 'urus' ),
                'category'                => esc_html__( 'Urus Elements', 'urus' ),
                'description'             => esc_html__( 'Display Tabs', 'urus' ),
                'is_container'            => true,
                'show_settings_on_create' => false,
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'as_parent'               => array(
                    'only' => 'vc_tta_section',
                ),
                'params'                  => array(

                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Title', 'urus' ),
                        'param_name'  => 'title',
                        'description' => esc_html__( 'The title of shortcode', 'urus' ),
                        'admin_label' => true,
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Layout', 'urus' ),
                        'param_name' => 'layout',
                        'value'      => array(
                            esc_html__( 'Default', 'urus' )   => 'default',
                            esc_html__( 'Layout 01', 'urus' )   => 'layout1',
                            esc_html__( 'Layout 02', 'urus' )   => 'layout2',
                            esc_html__( 'Layout 03', 'urus' )   => 'layout3',
                            esc_html__( 'Layout 04', 'urus' )   => 'layout4',
                        ),
                        'std'        => 'default',
                    ),
                    vc_map_add_css_animation(),
                    array(
                        'param_name' => 'ajax_check',
                        'heading'    => esc_html__( 'Using Ajax Tabs', 'urus' ),
                        'type'       => 'dropdown',
                        'value'      => array(
                            esc_html__( 'Yes', 'urus' ) => '1',
                            esc_html__( 'No', 'urus' )  => '0',
                        ),
                        'std'        => '0',
                    ),
                    array(
                        'type'       => 'number',
                        'heading'    => esc_html__( 'Active Section', 'urus' ),
                        'param_name' => 'active_section',
                        'std'        => 0,
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
                'js_view'                 => 'VcBackendTtaTabsView',
                'custom_markup'           => '
                    <div class="vc_tta-container" data-vc-action="collapse">
                        <div class="vc_general vc_tta vc_tta-tabs vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
                            <div class="vc_tta-tabs-container">'
                    . '<ul class="vc_tta-tabs-list">'
                    . '<li class="vc_tta-tab" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>'
                    . '</ul>
                            </div>
                            <div class="vc_tta-panels vc_clearfix {{container-class}}">
                              {{ content }}
                            </div>
                        </div>
                    </div>',
                'default_content'         => '
                        [vc_tta_section title="' . sprintf( '%s %d', esc_attr__( 'Tab', 'urus' ), 1 ) . '"][/vc_tta_section]
                        [vc_tta_section title="' . sprintf( '%s %d', esc_attr__( 'Tab', 'urus' ), 2 ) . '"][/vc_tta_section]
                    ',
                'admin_enqueue_js'        => array(
                    vc_asset_url( 'lib/vc_tabs/vc-tabs.min.js' ),
                ),
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}
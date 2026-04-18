<?php
if( !class_exists('Urus_Shortcodes_Person')){
    class Urus_Shortcodes_Person extends  Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'person';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_person', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_person_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_person', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-person' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_person', $atts );
            $socials= vc_param_group_parse_atts( $atts['socials'] );
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( $atts['avatar']):?>
                    <div class="avatar">
                        <?php echo wp_get_attachment_image($atts['avatar'],'full');?>
                    </div>
                <?php endif;?>
                <div class="infos">
                    <?php if( $atts['name']):?>
                    <h3 class="name"><?php echo esc_html($atts['name']);?></h3>
                    <?php endif;?>
                    <?php if( $atts['position']):?>
                    <div class="position"><?php echo esc_html($atts['position']);?></div>
                    <?php endif;?>
                    <?php if( !empty($socials)):?>
                    <div class="socials">
                        <?php foreach ($socials as $social):?>
                            <a href="<?php echo esc_url($social['url']);?>"><span class="<?php echo esc_attr($social['icon_fontawesome']);?>"></span></a>
                        <?php endforeach;?>    
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_person_output', ob_get_clean(), $atts, $content);
        }

        public function vc_map(){

            $params    = array(
                'base'        => 'urus_person',
                'name'        => esc_html__( 'Person', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display A Person', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Name', 'urus' ),
                        'param_name'    => 'name',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Position', 'urus' ),
                        'param_name'    => 'position',
                        'admin_label'   => true,
                    ),

                    array(
                        "type"        => 'attach_image',
                        "heading"     => esc_html__('Avatar', 'urus'),
                        "param_name"  => 'avatar',
                        "value"       => '',
                    ),
                    array(
                        'type' => 'param_group',
                        'value' => '',
                        'param_name' => 'socials',
                        'heading'    => esc_html__('Socials', 'urus'),
                        'params' => array(
                            array(
                                'type'       => 'textfield',
                                'value'      => '',
                                'heading'    => esc_html__('URL', 'urus'),
                                'param_name' => 'url',
                            ),
                            array(
                                'param_name'  => 'icon_fontawesome',
                                'heading'     => esc_html__( 'Icon', 'urus' ),
                                'type'        => 'iconpicker',
                                'settings'    => array(
                                    'emptyIcon' => false,
                                    'type'      => 'fontawesome',
                                ),
                            )

                        )
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
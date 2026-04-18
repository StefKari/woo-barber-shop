<?php
if( !class_exists('Urus_Helper')){
    class Urus_Helper{
        public static function get_option_c($key,$default=''){
            if(isset($_COOKIE[$key]) && !is_null($_COOKIE[$key]) && !empty($_COOKIE[$key])) {
                return $_COOKIE[$key];
            } else {
                return self::get_option($key,$default);
            }
        }
        public static function get_option( $key,$default ='',$filter = true){
            global $urus;
            if( empty($urus) ){
                $urus = get_option('urus',true);
                $GLOBALS['urus'] = $urus;
            }
            $value = $default;
            if( isset( $_GET[$key] )){
                $value =  $_GET[$key];
                return $value;
            }
            if(isset($urus[$key]) && $urus[$key] != ''){
                $value = $urus[$key];
            }
            if( $filter ){
                return apply_filters('urus_get_option',$value,$key);
            }else{
                return $value;
            }

        }
        public static function get_post_meta( $post_id ,$key, $default =''){
            $value = get_post_meta($post_id,$key,true);
            if( $value){
                return $value;
            }
            return $default;
        }
        public static function get_drawers(){
            $mobile_enable = Urus_Helper::get_option('enable_mobile_template', 1);
            $is_mobile = Urus_Helper::is_mobile_template();
            $mobile_header_style = Urus_Helper::get_option('mobile_header', 'style1' );
            ob_start();
            do_action('urus_before_drawers');
            if ($mobile_enable &&  $is_mobile ){
                get_template_part('template-parts/drawers', $mobile_header_style );

            }
            if(!$is_mobile){
                get_template_part('template-parts/drawers', 'style1' );
            }
            ?>

            <?php
            get_template_part('template-parts/wishlist-drawer');
            do_action('urus_after_drawers');
            $html = ob_get_contents();
            ob_end_clean();
            echo apply_filters('urus_display_drawers',$html);
        }
        public static function urus_wishlist_html() {
            $html = '';
            if ( function_exists( 'yith_wcwl_object_id' ) ) {
                $wishlist_page_id = yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) );
                $url              = get_the_permalink( $wishlist_page_id );
            }
            $wishlist_enable = Urus_Helper::get_option('enable_familab_wishlist',0);

            if ($wishlist_enable) {
                ob_start();
                ?>
                <div class="wishlist-mobile-menu-link-wrap">
                    <a href="javascript:void(0);" class="wishlist-mobile-menu-link js-urus-wishlist">
                    <?php esc_html_e( 'My Wishlist', 'urus' ); ?>
                    <span>
                        <?php echo familab_icons('heart'); ?>
                    </span>
                    </a>
                </div>
                <?php
                $html = ob_get_clean();
            }else{
                if( !class_exists('YITH_WCWL')) return '';
                $wishlist_link = get_permalink( get_option('yith_wcwl_wishlist_page_id') );
                ob_start();
                ?>
                <div class="wishlist-mobile-menu-link-wrap">
                    <a href="<?php echo esc_url($wishlist_link);?>">
                        <span class="text"><?php esc_html_e('Wishlist','urus')?></span>
                    </a>
                </div>
                <?php
                $html = ob_get_clean();
            }
            return $html;
        }
        public static function get_product_image($attachment_id, $size = 'thumbnail', $icon = false ){
            $thumb = wp_get_attachment_image_src(  $attachment_id ,$size, $icon );
            if ($thumb == false) {
                $width = 150;
                $height = 150;
                return "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $width . "%20" . $height . "%27%2F%3E";
            }else{
                return $thumb;
            }
        }

        public static function get_header(){
            $used_header = self::get_option('used_header','default');

            ob_start();
            do_action('urus_before_header');
            get_template_part('template-parts/header',$used_header);
            do_action('urus_after_header');
            $html = ob_get_contents();
            ob_end_clean();
            echo apply_filters('urus_display_header',$html,$used_header);
        }
        public static function header_mobile(){
            ob_start();
            do_action('urus_before_header_mobile');
            get_template_part('template-parts/header','mobile');
            do_action('urus_after_header_mobile');
            $html = ob_get_contents();
            ob_end_clean();
            echo apply_filters('urus_display_header',$html);
        }
        Public static function get_logo_align($type = ''){
            if ($type != '')
                $type .='_';
            $v_align = self::get_option($type.'logo_v_align','middle');
            $align = 'familab-v-'.$v_align;
            $h_align = self::get_option($type.'logo_h_align','left');
            $align .= ' familab-h-'.$h_align;
            return $align;
        }
        public static function get_logo_inner_style($type = ''){
            if ($type != '')
                $type .= '_';
            $style = '';
            $t = self::get_option($type.'logo_padding_top',0);
            $r = self::get_option($type.'logo_padding_right',0);
            $b = self::get_option($type.'logo_padding_bottom',0);
            $l = self::get_option($type.'logo_padding_left',0);
            if ($t == $b && $l==$r){
                if ($t == $l){
                    //if ($t != 0){
                        $style = 'padding:'.$t.'px;';
                    //}
                }else{
                    //if ($t != 0 || $l != 0) {
                        $style = 'padding:' . $t . 'px ' . $l . 'px;';
                    //}
                }
            }else{
                $style = 'padding-top:'.$t.'px;padding-right:'.$r.'px; padding-bottom:'.$b.'px;padding-left:'.$l.'px;';
            }
            return $style;
        }
        public static function get_logo( $type ='main',$e = true){
            $default_logo = array(
                'url'       => URUS_IMAGES . '/logo.svg',
                'id'        => '',
                'width'     => '80',
                'height'    => '29',
                'thumbnail' => '',
                'title'     => get_bloginfo('name')
            );
            $logo = self::get_option('logo',$default_logo);
            if( $type =='mobile'){
                $logo = self::get_option('logo_mobile',$default_logo);
            }
            $logo_url = $logo['url'];
            $html = '<a href="'.esc_url( get_home_url(get_current_blog_id()) ).'"><img alt="'.esc_attr__('Logo','urus').'" src="'.esc_url($logo_url).'" class="_rw" /></a>';
            $html = apply_filters( 'urus_theme_logo', $html );
            if ($e)
                echo e_data($html);
            else
                return $html;
        }
        public static function resize_image( $attachment_id = null, $width, $height, $crop = false, $use_lazy = false ){
            $original    = false;
            $image_src   = array();
            $enable_lazy = self::get_option( 'theme_use_lazy_load',0 );
            if ( $enable_lazy == 1  ){
                $use_lazy = true;
            }else{
                $use_lazy = false;
            }
            if(isset($_GET['action']) && $_GET['action'] =='elementor'){
                $use_lazy = false;
            }
            if (!$attachment_id && is_singular()) {
                if ( has_post_thumbnail() && !post_password_required() ) {
                    $attachment_id = get_post_thumbnail_id();
                }
            }
            if ( $attachment_id ) {
                $image_src        = wp_get_attachment_image_src( $attachment_id, 'full' );
                $actual_file_path = get_attached_file( $attachment_id );

            }
            if ( $width == false && $height == false ) {
                $original = true;
            }
            if ( !empty( $actual_file_path ) && file_exists( $actual_file_path ) ) {
                if ( $original == false && ( $image_src[1] > $width || $image_src[2] > $height ) ) {
                    $file_info        = pathinfo( $actual_file_path );
                    $extension        = '.' . $file_info['extension'];
                    $no_ext_path      = $file_info['dirname'] . '/' . $file_info['filename'];
                    $cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;
                    /* start */
                    if ( file_exists( $cropped_img_path ) ) {
                        $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
                        $vt_image        = array(
                            'url'    => $cropped_img_url,
                            'width'  => $width,
                            'height' => $height,
                            'img'    => self::get_attachment_image( $attachment_id, $cropped_img_url, $width, $height, $use_lazy ),
                        );

                        return $vt_image;
                    }
                    if ( !$crop ) {
                        $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
                        $resized_img_path  = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;
                        if ( file_exists( $resized_img_path ) ) {
                            $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
                            $vt_image        = array(
                                'url'    => $resized_img_url,
                                'width'  => $proportional_size[0],
                                'height' => $proportional_size[1],
                                'img'    => self::get_attachment_image( $attachment_id, $resized_img_url, $proportional_size[0], $proportional_size[1], $use_lazy ),
                            );

                            return $vt_image;
                        }else{
                            $vt_image        = array(
                                'url'    => $image_src[0],
                                'width'  => $image_src[1],
                                'height' => $image_src[2],
                                'img'    => self::get_attachment_image( $attachment_id, $image_src[0], $image_src[1], $image_src[2], $use_lazy ),
                            );
                            return $vt_image;
                        }
                    }
                    /*no cache files - let's finally resize it*/
                    $img_editor = wp_get_image_editor( $actual_file_path );
                    if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
                        return array(
                            'url'    => '',
                            'width'  => '',
                            'height' => '',
                            'img'    => '',
                        );
                    }
                    $new_img_path = $img_editor->generate_filename();
                    if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
                        return array(
                            'url'    => '',
                            'width'  => '',
                            'height' => '',
                            'img'    => '',
                        );
                    }
                    if ( !is_string( $new_img_path ) ) {
                        return array(
                            'url'    => '',
                            'width'  => '',
                            'height' => '',
                            'img'    => '',
                        );
                    }
                    $new_img_size = getimagesize( $new_img_path );
                    $new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );
                    $vt_image     = array(
                        'url'    => $new_img,
                        'width'  => $new_img_size[0],
                        'height' => $new_img_size[1],
                        'img'    => self::get_attachment_image( $attachment_id, $new_img, $new_img_size[0], $new_img_size[1], $use_lazy ),
                    );

                    return $vt_image;
                }
                $vt_image = array(
                    'url'    => $image_src[0],
                    'width'  => $image_src[1],
                    'height' => $image_src[2],
                    'img'    => self::get_attachment_image( $attachment_id, $image_src[0], $image_src[1], $image_src[2], $use_lazy ),
                );

                return $vt_image;
            } else {
                if( isset($image_src) && isset($image_src[0])){
                    $vt_image = array(
                        'url'    => $image_src[0],
                        'width'  => $image_src[1],
                        'height' => $image_src[2],
                        'img'    => self::get_attachment_image( $attachment_id, $image_src[0], $image_src[1], $image_src[2], $use_lazy ),
                    );
                    return $vt_image;
                }else{
                    $width           = $width == false ? 1 : intval( $width );
                    $height          = $height == false ? 1 : intval( $height );
                    $default_placeholder_src = URUS_IMAGES . '/product-placeholder.jpg';
                    $use_custom_placeholder = Urus_Helper::get_option('enable_custom_placeholder',false);

                    $placeholder_url = '';
                    if ($use_custom_placeholder ) {
                       $cropped_img_path = Urus_Pluggable_WooCommerce::urus_custom_woocommerce_placeholder( $default_placeholder_src );
                    }else{
                        $cropped_img_path = esc_url( '//via.placeholder.com/' . $width . 'x' . $height );
                    }

                    $url_placeholder = esc_url($cropped_img_path);

                    $vt_image        = array(
                        'url'    => $url_placeholder,
                        'width'  => $width,
                        'height' => $height,
                        'img'    => ( $original == false ) ? self::get_attachment_image( $attachment_id, $url_placeholder, $width, $height, false ) : '',
                    );
                    return $vt_image;
                }
            }
        }
        public static function get_attachment_image( $attachment_id, $src, $width, $height, $lazy ){
            $html     = '';
            $img_lazy = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $width . "%20" . $height . "%27%2F%3E";
            if ( $src ) {
                $hwstring   = image_hwstring( $width, $height );
                $size_class = $width . 'x' . $height;
                $attachment = get_post( $attachment_id );
                $attr       = array(
                    'src'   => $src,
                    'class' => "img-responsive wp-post-image attachment-$size_class size-$size_class",
                    'alt'   => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
                );
                if ( $lazy == true ) {
                    $attr['src']      = $img_lazy;
                    $attr['data-src'] = $src;
                    $attr['class']    .= ' lazy';
                }

                /**
                 * Filters the list of attachment image attributes.
                 *
                 * @since 2.8.0
                 *
                 * @param array $attr Attributes for the image markup.
                 * @param WP_Post $attachment Image attachment post.
                 * @param string|array $size Requested size. Image size or array of width and height values
                 *                                 (in that order). Default 'thumbnail'.
                 */
                $attr = apply_filters( 'urus_get_attachment_image_attributes', $attr, $attachment );
                $attr = array_map( 'esc_attr', $attr );
                $html = rtrim( "<img $hwstring" );
                foreach ( $attr as $name => $value ) {
                    $html .= " $name=" . '"' . $value . '"';
                }
                $html .= ' />';
            }

            return $html;
        }
        public static function post_thumb(){
            global $wp_query;
            $blog_layout = self::get_option('blog_layout','left');
            $theme_use_placeholder = self::get_option('theme_use_placeholder',0);
            $blog_list_style = Urus_Helper::get_option('blog_list_style','classic');
            if( !has_post_thumbnail() && $theme_use_placeholder == 0) return false;
            $crop = false;
            if( $blog_layout =='full' ){
                $width = 1040;
                $height = 649;


            }else{
                $width = 1040;
                $height = 649;


            }
            if( $blog_list_style == 'grid'){
                $width = 500;
                $height = 500;
                $crop = true;
            }

            if( is_single()){
                $width = 1405;
                $height = 765;

            }
            $thumb = '';

            if( $crop == false){
                $height = false;
            }

            $image_thumb = self::resize_image( get_post_thumbnail_id(), $width, $height, $crop,true );
            if( isset($image_thumb['img']) && $image_thumb['img'] !=""){
                $thumb = $image_thumb['img'];
            }

            if( $thumb!=""){
                ?>
                <div class="post-thumb">
                    <?php if($blog_list_style =='grid'):?>
                        <span class="date">
                            <span class="day"><?php echo get_the_date('d');?></span>
                            <span class="month"><?php echo get_the_date('M');?></span>
                        </span>
                    <?php endif;?>
                    <?php
                    if( is_single()){
                        echo Urus_Helper::escaped_html($thumb);
                    }else{
                        ?>
                        <a href="<?php the_permalink();?>"><figure><?php echo Urus_Helper::escaped_html($thumb);?></figure></a>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        public static function comment_nav() {
            // Are there comments to navigate through?
            if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
                ?>
                <nav class="navigation comment-navigation" role="navigation">
                    <h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'urus' ); ?></h2>
                    <div class="nav-links">

                        <?php
                        if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'urus' ) ) ) :
                            printf( '<div class="nav-previous">%s</div>', $prev_link );
                        endif;

                        if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'urus' ) ) ) :
                            printf( '<div class="nav-next">%s</div>', $next_link );
                        endif;
                        ?>
                    </div><!-- .nav-links -->
                </nav><!-- .comment-navigation -->
            <?php
            endif;
        }
        public static function escaped_html( $html){
            $html = trim($html);
            return $html;
        }
        public static function get_category(){
            $category = get_the_category();
            $currentcat = $category[0]->cat_ID;
            $currentcatname = $category[0]->cat_name;
            $currentcatslug = $category[0]->slug;
            $link = get_category_link( $currentcat );
            ?>
            <span class="post-categories">
                <a href="<?php echo esc_url($link);?>"><?php echo esc_html($currentcatname);?></a>
            </span>
            <?php
        }
        /**
         * Get the current page URL
         *
         * @since 1.0
         * @param  bool   $nocache  If we should bust cache on the returned URL
         * @return string $page_url Current page URL
         */
        public static function get_current_page_url( ) {
            global $wp;
            if( get_option( 'permalink_structure' ) ) {
                $base = trailingslashit( home_url( $wp->request ) );
            } else {
                $base = add_query_arg( $wp->query_string, '', trailingslashit( home_url( $wp->request ) ) );
                $base = remove_query_arg( array( 'post_type', 'name' ), $base );

            }
            $scheme = is_ssl() ? 'https' : 'http';
            $uri    = set_url_scheme( $base, $scheme );
            if ( is_front_page() ) {
                $uri = home_url( '/' );
            }
            $uri = apply_filters( 'urus_get_current_page_url', $uri );
            return $uri;
        }
        public static function carousel_data_attributes($prefix = '', $atts){
            $responsive = array();
            $slick      = array();

            $results    = '';
            if ( isset( $atts[$prefix . 'autoplay'] ) && $atts[$prefix . 'autoplay'] == 'true' ) {
                $slick['autoplay'] = true;
                if ( isset( $atts[$prefix . 'autoplayspeed'] ) && $atts[$prefix . 'autoplay'] == 'true' ) {
                    $slick['autoplaySpeed'] = intval( $atts[$prefix . 'autoplayspeed'] );
                }
            }
            if ( isset( $atts[$prefix . 'navigation'] ) ) {
                $slick['arrows'] = $atts[$prefix . 'navigation'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'slide_margin'] ) ) {
                $slick['slidesMargin'] = intval( $atts[$prefix . 'slide_margin'] );
            }
            if ( isset( $atts[$prefix . 'dots'] ) ) {
                $slick['dots'] = $atts[$prefix . 'dots'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'loop'] ) ) {
                $slick['infinite'] = $atts[$prefix . 'loop'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'fade'] ) ) {
                $slick['fade'] = $atts[$prefix . 'fade'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'slidespeed'] ) ) {
                $slick['speed'] = intval( $atts[$prefix . 'slidespeed'] );
            }
            if ( isset( $atts[$prefix . 'ls_items'] ) ) {
                $slick['slidesToShow'] = intval( $atts[$prefix . 'ls_items'] );
            }
            if ( isset( $atts[$prefix . 'vertical'] ) && $atts[$prefix . 'vertical'] == 'true' ) {
                $slick['vertical'] = true;
                if ( isset( $atts[$prefix . 'verticalswiping'] ) && $atts[$prefix . 'verticalswiping'] == 'true' ) {
                    $slick['verticalSwiping'] = true;
                }
            }
            if ( isset( $atts[$prefix . 'center_mode'] ) && $atts[$prefix . 'center_mode'] == 'true' ) {
                $slick['centerMode'] = true;
                if ( isset( $atts[$prefix . 'center_padding'] ) ) {
                    $slick['centerPadding'] = $atts[$prefix . 'center_padding'] . 'px';
                }
            }
            if ( isset( $atts[$prefix . 'focus_select'] ) && $atts[$prefix . 'focus_select'] == 'true' ) {
                $slick['focusOnSelect'] = true;
            }
            if ( isset( $atts[$prefix . 'number_row'] ) ) {
                $slick['rows'] = intval( $atts[$prefix . 'number_row'] );
            }

            if ( isset( $atts[$prefix . 'fade'] ) && $atts[$prefix . 'fade'] =='true' ) {
                $slick['fade'] = true;
                $slick['cssEase'] = 'linear';
            }

            $slick   = apply_filters( 'urus_carousel_slick_attributes', $slick, $prefix, $atts );
            $results .= ' data-slick = ' . json_encode( $slick ) . ' ';
            if( isset($atts[$prefix.'navigation_style']) && $atts[$prefix.'navigation_style']!=''){
                $results .= ' data-nav = ' . $atts[$prefix.'navigation_style'] . ' ';
            }

            /* RESPONSIVE */
            if( did_action( 'elementor/loaded' )){
                $responsive = apply_filters( 'urus_elementer_carousel_responsive_attributes', [], $prefix, $atts );
                $results    .= 'data-builder = "elementor" ';
            } else {
                $slick_responsive = self::data_responsive_carousel();
                foreach ( $slick_responsive as $key => $item ) {
                    if ( isset( $atts[$prefix . $item['name']] ) && intval( $atts[$prefix . $item['name']] ) > 0 ) {
                        $responsive[$key] = array(
                            'breakpoint' => $item['screen'],
                            'settings'   => array(
                                 'slidesToShow' => intval( $atts[$prefix . $item['name']] ),
                            ),
                        );
                        if( isset($atts['responsive_settings'][$item['screen']]) && !empty($atts['responsive_settings'][$item['screen']])){
                            $settings = Urus_Helper::get_reponsive_settings('',$atts['responsive_settings'][$item['screen']]);
                            if( !empty($settings)){
                                $responsive[$key]['settings'] = array_merge( $responsive[$key]['settings'], $settings );
                            }
                        }
                        if ( isset( $item['settings'] ) && !empty( $item['settings'] ) ){
                            if ( isset( $atts[$prefix . 'slide_margin'] ) && $atts[$prefix . 'slide_margin'] <=0 && isset( $item['settings']['slidesMargin'] )) {
                                $item['settings']['slidesMargin'] = 0;
                            }
                            $responsive[$key]['settings'] = array_merge( $responsive[$key]['settings'], $item['settings'] );
                        }
                        /* RESPONSIVE VERTICAL */
                        if ( isset( $atts[$prefix . 'responsive_vertical'] ) && $atts[$prefix . 'responsive_vertical'] >= $item['screen'] ) {
                            $responsive[$key]['settings']['vertical'] = false;
                            if ( isset( $atts[$prefix . 'slide_margin'] ) ) {
                                $responsive[$key]['settings']['slidesMargin'] = intval( $atts[$prefix . 'slide_margin'] );
                            }
                        }
                    }
                }
                $responsive = apply_filters( 'urus_carousel_responsive_attributes', $responsive, $prefix, $atts );
            }
            $results    .= 'data-responsive = ' . json_encode( array_values( $responsive ) ) . ' ';
            return wp_specialchars_decode( $results );
        }
        public static function get_reponsive_settings( $prefix = '', $atts){
            $slick      = array();
            if ( isset( $atts[$prefix . 'autoplay'] ) && $atts[$prefix . 'autoplay'] == 'true' ) {
                $slick['autoplay'] = true;
                if ( isset( $atts[$prefix . 'autoplayspeed'] ) && $atts[$prefix . 'autoplay'] == 'true' ) {
                    $slick['autoplaySpeed'] = intval( $atts[$prefix . 'autoplayspeed'] );
                }
            }
            if ( isset( $atts[$prefix . 'navigation'] ) ) {
                $slick['arrows'] = $atts[$prefix . 'navigation'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'slide_margin'] ) ) {
                $slick['slidesMargin'] = intval( $atts[$prefix . 'slide_margin'] );
            }
            if ( isset( $atts[$prefix . 'dots'] ) ) {
                $slick['dots'] = $atts[$prefix . 'dots'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'loop'] ) ) {
                $slick['infinite'] = $atts[$prefix . 'loop'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'fade'] ) ) {
                $slick['fade'] = $atts[$prefix . 'fade'] == 'true' ? true : false;
            }
            if ( isset( $atts[$prefix . 'slidespeed'] ) ) {
                $slick['speed'] = intval( $atts[$prefix . 'slidespeed'] );
            }
            if ( isset( $atts[$prefix . 'ls_items'] ) ) {
                $slick['slidesToShow'] = intval( $atts[$prefix . 'ls_items'] );
            }
            if ( isset( $atts[$prefix . 'vertical'] ) && $atts[$prefix . 'vertical'] == 'true' ) {
                $slick['vertical'] = true;
                if ( isset( $atts[$prefix . 'verticalswiping'] ) && $atts[$prefix . 'verticalswiping'] == 'true' ) {
                    $slick['verticalSwiping'] = true;
                }
            }
            if ( isset( $atts[$prefix . 'center_mode'] ) && $atts[$prefix . 'center_mode'] == 'true' ) {
                $slick['centerMode'] = true;
                if ( isset( $atts[$prefix . 'center_padding'] ) ) {
                    $slick['centerPadding'] = $atts[$prefix . 'center_padding'] . 'px';
                }
            }
            if ( isset( $atts[$prefix . 'focus_select'] ) && $atts[$prefix . 'focus_select'] == 'true' ) {
                $slick['focusOnSelect'] = true;
            }
            if ( isset( $atts[$prefix . 'number_row'] ) ) {
                $slick['rows'] = intval( $atts[$prefix . 'number_row'] );
            }
            if ( isset( $atts[$prefix . 'fade'] ) && $atts[$prefix . 'fade'] =='true' ) {
                $slick['fade'] = true;
                $slick['cssEase'] = 'linear';
            }
            return $slick;
        }
        public static function data_responsive_carousel()
        {
            $responsive = array(
                'desktop'          => array(
                    'screen'   => 1500,
                    'name'     => 'lg_items',
                    'title'    => esc_html__( 'The items on desktop (Screen resolution of device >= 1200px and < 1500px )', 'urus' ),
                    'settings' => array(),
                ),
                'laptop'           => array(
                    'screen'   => 1200,
                    'name'     => 'md_items',
                    'title'    => esc_html__( 'The items on desktop (Screen resolution of device >= 992px < 1200px )', 'urus' ),
                    'settings' => array(),
                ),
                'tablet'           => array(
                    'screen'   => 992,
                    'name'     => 'sm_items',
                    'title'    => esc_html__( 'The items on tablet (Screen resolution of device >=768px and < 992px )', 'urus' ),
                    'settings' => array(),
                ),
                'mobile_landscape' => array(
                    'screen'   => 768,
                    'name'     => 'xs_items',
                    'title'    => esc_html__( 'The items on mobile landscape(Screen resolution of device >=480px and < 768px)', 'urus' ),
                    'settings' => array(
                        'slidesMargin'=>15
                    ),
                ),
                'mobile'           => array(
                    'screen'   => 480,
                    'name'     => 'ts_items',
                    'title'    => esc_html__( 'The items on mobile (Screen resolution of device < 480px)', 'urus' ),
                    'settings' => array(
                        'slidesMargin'=>15
                    ),
                ),
            );

            return apply_filters( 'urus_carousel_responsive_screen', $responsive );
        }
        public static function get_all_social(){
            $socials = array(
                'opt_twitter_link' => array(
                    'name' => 'Twitter',
                    'id'=>'opt_twitter_link',
                    'icon'=>'<i class="fa fa-twitter"></i>'
                ),
                'opt_fb_link' => array(
                    'name' => 'Facebook',
                    'id'=>'opt_fb_link',
                    'icon'=>'<i class="fa fa-facebook"></i>'
                ),
                'opt_google_plus_link' => array(
                    'name' => 'Google plus',
                    'id'=>'opt_google_plus_link',
                    'icon'=>'<i class="fa fa-google-plus" aria-hidden="true"></i>'
                ),
                'opt_dribbble_link' => array(
                    'name' => 'Dribbble',
                    'id'=>'opt_dribbble_link',
                    'icon'=>'<i class="fa fa-dribbble" aria-hidden="true"></i>'
                ),
                'opt_behance_link' => array(
                    'name' => 'Behance',
                    'id'=>'opt_behance_link',
                    'icon'=>'<i class="fa fa-behance" aria-hidden="true"></i>'
                ),
                'opt_tumblr_link' => array(
                    'name' => 'Tumblr',
                    'id'=>'opt_tumblr_link',
                    'icon'=>'<i class="fa fa-tumblr" aria-hidden="true"></i>'
                ),
                'opt_instagram_link' => array(
                    'name' => 'Instagram',
                    'id'=>'opt_instagram_link',
                    'icon'=>'<i class="fa fa-instagram" aria-hidden="true"></i>'
                ),
                'opt_pinterest_link' => array(
                    'name' => 'Pinterest',
                    'id'=>'opt_pinterest_link',
                    'icon'=>'<i class="fa fa-pinterest" aria-hidden="true"></i>'
                ),
                'opt_youtube_link'=> array(
                    'name' => 'Youtube',
                    'id'=>'opt_youtube_link',
                    'icon'=>'<i class="fa fa-youtube" aria-hidden="true"></i>'
                ),
                'opt_vimeo_link' => array(
                    'name' => 'Vimeo',
                    'id'=>'opt_vimeo_link',
                    'icon'=>'<i class="fa fa-vimeo" aria-hidden="true"></i>'
                ),
                'opt_linkedin_link' => array(
                    'name' => 'Linkedin',
                    'id'=>'opt_linkedin_link',
                    'icon'=>'<i class="fa fa-linkedin" aria-hidden="true"></i>'
                ),
                'opt_rss_link' => array(
                    'name' => 'RSS',
                    'id'=>'opt_rss_link',
                    'icon'=>'<i class="fa fa-rss" aria-hidden="true"></i>'
                )
            );
            return apply_filters('urus_get_all_social_links',$socials);
        }
        public static function display_social( $social='', $show_name= false){
            $all_social = Urus_Helper::get_all_social();
            $social_link = Urus_Helper::get_option($social,'');

            $social_icon = $all_social[$social]['icon'];
            $social_name = $all_social[$social]['name'];
            $text = $social_icon;
            if( $show_name ){
                $text .= ' <span class="text">'. $social_name.'</span>';
            }
            echo Urus_Helper::escaped_html('<a class="social" target="_blank" href="'.esc_url($social_link).'" title ="'.esc_attr($social_name).'" >'.$text.'</a>');
        }
        public static function pinmapper_options(){
            $args           = array(
                'post_type'      => 'urus_pinmap',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            );
            $pinmap_loop    = new wp_query( $args );
            $pinmap_options = array();
            while ( $pinmap_loop->have_posts() ) {
                $pinmap_loop->the_post();
                $pinmap_options[get_the_title()] =get_the_ID();
            }
            wp_reset_postdata();
            return $pinmap_options;
        }
        public static function header_socials(){
            $header_used_socials = Urus_Helper::get_option('header_used_socials','');
            ?>
            <?php if( !empty($header_used_socials)):?>
                <div class="header-socials socials">
                    <?php foreach ($header_used_socials as $social):?>
                        <?php Urus_Helper::display_social($social)?>
                    <?php endforeach;?>
                </div>
            <?php endif;?>
            <?php
        }
        public static function header_multi_text(){
            $multi_header_text = Urus_Helper::get_option('multi_header_text',array());
            if( !empty($multi_header_text)){
                ?>
                <div class="header-multi-text">
                    <div class="inner">
                        <div class="items">
                            <?php foreach ($multi_header_text as $text):?>
                                <div class="text"><?php echo esc_html($text);?></div>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        public static function get_main_menu($type = 'classic_menu'){
            if(has_nav_menu('primary')){
                if ($type == 'classic_menu'){
                    if( has_nav_menu('primary')){
                        wp_nav_menu( array(
                            'menu'            => 'primary',
                            'theme_location'  => 'primary',
                            'container'       => '',
                            'container_class' => '',
                            'container_id'    => '',
                            'menu_class'      => 'urus-nav main-menu  urus-clone-mobile-menu',
                        ));
                    }

                }else{
                    echo '<div class="main-menu-btn model_menu_btn '.$type.'"><span class="model-menu-icon"><span></span><span></span><span></span></span><span class="model-menu-title">';
                    esc_html_e('Menu','urus');
                    echo '</span></div>';
                    echo '<div class="main-menu-content model_menu_wrapper '.$type.'">';
                    echo '<a href="#" class="close_model_menu">';
                    echo familab_icons('close');
                    echo '</a>';
                    if(has_nav_menu('hamburger_menu')){
                        ?>

                        <div class="model_menu__inner">
                            <a href="#" class="prev-menu"><?php esc_html_e('Prev','urus');?></a>
                            <?php
                                wp_nav_menu( array(
                                    'menu'            => 'hamburger_menu',
                                    'theme_location'  => 'hamburger_menu',
                                    'container'       => '',
                                    'container_class' => '',
                                    'container_id'    => '',
                                    'menu_class'      => 'hamburger-menu urus-clone-mobile-menu menu-morph',
                                    'walker' =>new Urus_Walker()
                                ));
                            ?>
                        </div>
                        <?php

                    }

                    echo '</div>';
                }
            }
        }
        public static function get_promo_header(){
            $enable_header_promo = Urus_Helper::get_option('enable_header_promo',0);
            if (!$enable_header_promo){
                return;
            }
            $promo_content = Urus_Helper::get_option('promo_text');
            ?>
            <div class="promo-wrapper">
                <div class="header-promo">
                    <div class="container-wapper">
                        <div class="header-promo-content">
                            <div class="header-promo-text">
                                <?php echo apply_filters('urus_promo_text',$promo_content); ?>
                            </div>
                            <a class="header-promo-control" href="#">
                                <?php echo familab_icons('close'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        public static function is_mobile_template(){
            $result =  false;
            if (Urus_Mobile_Detect::isMobile()){
                $result = self::get_option('enable_mobile_template', 1);
            }
            return $result;
        }
        public static function get_menu_has_logo(){
            echo '<div class="grid-item">';
            add_filter( 'wp_nav_menu_items',array(__CLASS__,'insert_logo_to_menu_items'), 10, 2 );
            add_filter( 'wp_nav_menu_objects',array(__CLASS__,'update_menu_objects'), 10, 2 );
            wp_nav_menu( array(
                'menu'            => 'primary',
                'theme_location'  => 'primary',
                'container'       => '',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => 'urus-nav main-menu left',
            ));
            echo '</div>';
        }
        public static function insert_logo_to_menu_items($items, $args){
            if ($args->theme_location == 'primary'){
                $used_header = self::get_option('used_header', 'default');
                if ($used_header == 'logo_in_menu' || $used_header == 'logo_in_menu_line'){
                    $menu_str = explode('menu-item-middle',$items);
                    $insert_pos = strrpos($menu_str[0],'</li>');
                    $logo_str = '</ul></div><div class="grid-item"><ul class="urus-nav main-menu  center"><li class="menu-item menu-item-logo logo">'.self::get_logo('main',false).'</li></ul></div><div class="grid-item"><ul class="urus-nav main-menu  right">';
                    $str_insert = substr_replace($menu_str[0],$logo_str,$insert_pos+5,0) ;
                    $items = $str_insert.$menu_str[1];
                }
            }
            return $items;
        }
        public static function update_menu_objects($items, $args){
            if ($args->theme_location == 'primary'){
                $root_items = array();
                foreach ($items as $item){
                    if ($item->menu_item_parent == 0){
                        $root_items[] = $item;
                    }
                }
                $mid_key =  ceil (sizeof($root_items)/2);
                $mid_id = $root_items[$mid_key]->ID;
                $mid_class = $root_items[$mid_key]->classes;
                $mid_class[] = 'menu-item-middle';
                foreach ($items as $k => $item){
                    if ($item->ID == $mid_id){
                        $items[$k]->classes = $mid_class;
                    }
                }
            }
            return $items;
        }

        public static function hexToRgb($hex, $alpha = false) {
            $hex      = str_replace('#', '', $hex);
            $length   = strlen($hex);
            $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
            $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
            $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
            if ( $alpha ) {
                $rgb['a'] = $alpha;
            }
            return $rgb;
        }

        public static function lumdiff($R1,$G1,$B1,$R2,$G2,$B2){
            $L1 = 0.2126 * pow($R1/255, 2.2) +
                0.7152 * pow($G1/255, 2.2) +
                0.0722 * pow($B1/255, 2.2);

            $L2 = 0.2126 * pow($R2/255, 2.2) +
                0.7152 * pow($G2/255, 2.2) +
                0.0722 * pow($B2/255, 2.2);

            if($L1 > $L2){
                return ($L1+0.05) / ($L2+0.05);
            }else{
                return ($L2+0.05) / ($L1+0.05);
            }
        }
        public static function show_cat(){
            return false;
        }
    }
}

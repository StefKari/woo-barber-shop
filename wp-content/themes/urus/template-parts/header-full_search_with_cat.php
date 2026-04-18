<?php
$enable_sticky_header = Urus_Helper::get_option( 'enable_sticky_header', 0 );

$header_class = array( 'header search-has-categories' );
$sticky = "";
if ( $enable_sticky_header ) {
	$sticky = 'header-sticky';
}
$header_light = Urus_Helper::get_option( 'header_dark', 0 );
$enable_vertical_menu = Urus_Helper::get_option( 'enable_vertical_menu', 0 );
if ( $header_light == 1 ) {
	$header_class[] = 'dark';
}
?>
<header id="header" class="<?php echo esc_attr( implode( ' ', $header_class ) ); ?>">
    <div class="header-top-menu">
        <div class="container-wapper">
	        <?php if ( has_nav_menu( 'top_left_menu' ) ): ?>
                <?php
                $top_left_menu = wp_nav_menu( array(
                    'menu'            => 'top_left_menu',
                    'theme_location'  => 'top_left_menu',
                    'container'       => '',
                    'container_class' => '',
                    'container_id'    => '',
                    'menu_class'      => 'header-top-left-menu',
                ));
                ?>
            <?php endif; ?>
	        <?php if ( has_nav_menu( 'top_right_menu' ) ): ?>
		        <?php
		        $top_left_menu = wp_nav_menu( array(
			        'menu'            => 'top_right_menu',
			        'theme_location'  => 'top_right_menu',
			        'container'       => '',
			        'container_class' => '',
			        'container_id'    => '',
			        'menu_class'      => 'header-top-right-menu',
		        ));
		        ?>
	        <?php endif; ?>
        </div>
    </div>
    <div class="container-wapper">
        <div class="main-header">
            <div class="logo">
				<?php Urus_Helper::get_logo(); ?>
            </div>
			<?php Urus_Pluggable_WooCommerce::header_search_form( false, false, true , null, true); ?>
			<?php
			ob_start();
			do_action( 'urus_header_right_control' );
			$html = ob_get_clean();
			?>
			<?php if ( $html != '' ): ?>
                <div class="header-control right">
					<?php echo Urus_Helper::escaped_html( $html ); ?>
                </div>
			<?php endif; ?>
        </div>

    </div>
    <div class="main-menu-wapper menu-left <?php echo esc_attr($sticky) ?>">
        <div class="container-wapper clearfix">
            <div id="urus-menu-wapper"></div>
			<?php if ( has_nav_menu( 'vertical_menu' ) && $enable_vertical_menu ): ?>
				<?php
                    $content_vertical = wp_nav_menu( array(
                        'menu'            => 'vertical_menu',
                        'theme_location'  => 'vertical_menu',
                        'container'       => '',
                        'container_class' => '',
                        'container_id'    => '',
                        'menu_class'      => 'urus-nav vertical-menu  urus-clone-mobile-menu',
                        'echo'            => false,
                    ));
                    $always_open =  Urus_Helper::get_option( 'always_open_vertical_menu', 0 );
                    $default_open =  Urus_Helper::get_option( 'vertical_opened', 0 );
                    $vertical_menu_class[] = $always_open ? "always-open" : "";
                    $vertical_menu_class[] = $default_open ? "menu-open" : "";
				?>
                <?php if (!empty($content_vertical)): ?>
                    <div class="vertical-wrapper urus-nav block-nav-category <?php echo esc_attr(implode(' ', $vertical_menu_class)); ?>">
                        <a href="#" class="block-title">
                            <span class="before">
                                <span></span> <span></span><span></span>
                            </span>
                            <span class="text-title"><?php echo esc_html__("All Categories", "urus") ?></span>
                        </a>
                        <div class="block-content verticalmenu-content">
                            <?php echo Urus_Helper::escaped_html($content_vertical); ?>
                        </div>
                    </div>
                <?php endif; ?>
			<?php endif; ?>
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'menu'            => 'primary',
					'theme_location'  => 'primary',
					'container'       => '',
					'container_class' => '',
					'container_id'    => '',
					'menu_class'      => 'urus-nav main-menu  urus-clone-mobile-menu',
				) );
			}

			if ( has_nav_menu( 'extend_primary' ) ) {
				wp_nav_menu( array(
					'menu'            => 'extend_primary',
					'theme_location'  => 'extend_primary',
					'container'       => '',
					'container_class' => '',
					'container_id'    => '',
					'menu_class'      => 'urus-nav extend-primary-menu  urus-clone-mobile-menu',
				) );
			}
			?>
        </div>
    </div>
</header>
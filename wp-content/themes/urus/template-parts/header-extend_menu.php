<?php
$enable_sticky_header = Urus_Helper::get_option( 'enable_sticky_header', 0 );

$header_class = array( 'header extend_menu' );
$sticky = "";
if ( $enable_sticky_header ) {
	$sticky = 'header-sticky';
}
$header_light = Urus_Helper::get_option( 'header_dark', 0 );
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
	<div id="menu-extend-wrapper" class="<?php echo esc_attr($sticky) ?>">
        <div class="container-wapper">
            <div class="main-header">
                <div class="logo">
					<?php Urus_Helper::get_logo(); ?>
                </div>
                <div class="main-menu-wapper">
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
					?>
                </div>
				<?php
				ob_start();
				do_action('urus_header_right_control');
				$html = ob_get_clean();
				?>
				<?php if($html != ''):?>
                    <div class="header-control right">
						<?php echo Urus_Helper::escaped_html($html);?>
                    </div>
				<?php endif;?>
            </div>

        </div>
		<?php
		if ( has_nav_menu( 'extend_primary' ) ): ?>
            <div class="extend-menu-wapper">
                <div class="container-wapper clearfix">
					<?php
					wp_nav_menu( array(
						'menu'            => 'extend_primary',
						'theme_location'  => 'extend_primary',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => 'urus-nav extend-primary-menu  urus-clone-mobile-menu',
					) );
					?>
                </div>
            </div>
		<?php endif; ?>
    </div>
</header>
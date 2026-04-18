<?php
$enable_sticky_header = Urus_Helper::get_option('enable_sticky_header',0);
$header_class = array('header logo_menu_center menu_dark');
$sticky = "";
if ($enable_sticky_header){
	$sticky = 'header-sticky';
}
$header_light = Urus_Helper::get_option('header_dark',0);
if($header_light == 1){
	$header_class[] ='dark';
}
?>
<header id="header" class="<?php echo esc_attr( implode( ' ', $header_class ) ); ?>">
	<div class="container-wapper">
		<div class="main-header">
			<div class="main-header-columns">
				<div class="header-control left">
					<?php do_action('urus_header_left_control'); ?>
				</div>
				<div class="header-control-logo">
					<div class="logo text-center"><?php Urus_Helper::get_logo();?></div>
				</div>
				<div class="header-control right">
					<?php do_action('urus_header_right_control');?>
				</div>
			</div>
		</div>
	</div>
	<div id="urus-menu-wapper" class="<?php echo esc_attr($sticky) ?>">
		<div class="main-menu-wapper menu-center">
			<?php
			if(has_nav_menu('primary')){
				wp_nav_menu( array(
					'menu'            => 'primary',
					'theme_location'  => 'primary',
					'container'       => '',
					'container_class' => '',
					'container_id'    => '',
					'menu_class'      => 'urus-nav main-menu  urus-clone-mobile-menu',
				));
			}
			?>
		</div>
	</div>
</header>
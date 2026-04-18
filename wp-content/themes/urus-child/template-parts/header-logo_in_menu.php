<?php
$enable_sticky_header = Urus_Helper::get_option('enable_sticky_header',0);
$header_class = array('header logo_menu_center');
if ($enable_sticky_header){
    $header_class[] = 'header-sticky';
}
$header_light = Urus_Helper::get_option('header_dark',0);
if($header_light == 1){
    $header_class[] ='dark';
}

 
?>
<header id="header" class="<?php echo esc_attr( implode( ' ', $header_class ) ); ?>">
    <div class="container-wapper">
<?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
        <div id="urus-menu-wapper"></div>
        <div class="main-header">
            <div class="main-header-columns">
                
                <?php //echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
                <div class="header-control left">
                    <!-- <?php //do_action('urus_header_left_control');?> -->
                    <i id="search-btn-custom" class="fa fa-search search-btn-custom"  onclick="toggleSearchButton()"></i>
                    <i id="close-btn-custom" class="fas fa-times search-btn-custom hide"  onclick="toggleSearchButton()"></i>
                </div>
                <div class="header-control-center">
                    <div class="logo-in-menu">
                        <?php Urus_Helper::get_menu_has_logo();?>
                    </div>
                </div>
                <div class="header-control right">
                    <?php do_action('urus_header_right_control');?>
                </div>
            </div>
        </div>
    </div>
</header>
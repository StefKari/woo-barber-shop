<?php
global $woocommerce;
$args = array(
    'echo' => false
);
$breadcrumb = new Breadcrumb_Trail( $args );
$items = isset($breadcrumb->items) ? $breadcrumb->items :array();
$title = (!empty($items)) ? end($items) :'';

$mobile_header_style = Urus_Helper::get_option('mobile_header', 'style1' );
if (Urus_Helper::is_mobile_template()) {
  get_template_part('template-parts/header-mobile-'.$mobile_header_style );
}else{
?>
    <div class="header-mobile-responsive">
        <?php
            get_template_part('template-parts/header-mobile-style1');
        ?>
    </div>
<?php
}

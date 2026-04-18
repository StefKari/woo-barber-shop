<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="facebook-domain-verification" content="pl5mm55cwi1qc96o7ekmjjt02gl4e8" />
    <link rel="icon" type="image/png"  href="<?php echo bloginfo('template_directory');?>/assets/images/favicon.jpg" />
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<script>
    jQuery( document ).ready(function() {
        // alert("test");
        jQuery('.aws-search-btn_icon').click(function () {
            jQuery(".aws-wrapper").fadeToggle(800);
            jQuery( ".aws-search-form" ).toggleClass( "flex-row-custom" );
        });

    });


</script>

<body <?php body_class(); ?>>
<div id="overlay" class="overlay hide" onclick="toggleSearchButton()"></div>
<div id="overlay-mobile" class="overlay-mobile hide" onclick="toggleSearchButtonMobile()"></div>
<script>
    function toggleSearchButton() {
        document.getElementById("search-btn-custom").classList.toggle("hide");
        document.getElementById("close-btn-custom").classList.toggle("hide");
        document.getElementById("overlay").classList.toggle("hide");
        document.getElementsByClassName("asl_w_container")[0].classList.toggle("hide"); 
    }
    function toggleSearchButtonMobile() {
        document.getElementById("search-btn-custom-mobile").classList.toggle("hide");
        document.getElementById("close-btn-custom-mobile").classList.toggle("hide");
        document.getElementsByClassName("overlay-mobile")[0].classList.toggle("hide");
    try {
        document.getElementsByClassName("asl_w_container")[1].classList.toggle("hide");  
    } catch (error) {}
   
    try {
        document.querySelector("div.asl_w_container.asl_w_container_1").classList.toggle("hide");
    } catch (error) {}
}
</script>
<?php do_action('urus_before_content');?>
<?php Urus_Helper::get_drawers(); ?>
<div class="site-content">
    <?php do_action('urus_before_site_content');?>
	<?php
		if ( ! Urus_Helper::is_mobile_template()) {
			Urus_Helper::get_header();
		}
	?>





<?php Urus_Helper::header_mobile();?>


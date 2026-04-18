
<?php do_action('urus_after_site_content');?>
</div>


<!-- .site-content -->
<?php
$enable_back_to_top_button = Urus_Helper::get_option('enable_back_to_top_button',0);
$back_to_top_button_layout = Urus_Helper::get_option('back_to_top_button_layout','default');
$class = array('backtotop hint--bounce hint--left');
$class[] = $back_to_top_button_layout;
?>
<?php if( $enable_back_to_top_button == 1):?>
<a href="#" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>" aria-label="<?php esc_attr_e('Go to top','urus');?>">
    <?php if($back_to_top_button_layout=='percent_circle'):?>
        <svg class="backtotop-round" viewbox="0 0 100 100" width="50" height="50">
            <circle cx="50" cy="50" r="40" />
        </svg>
        <i class="icon fa fa-angle-up"></i>
    <?php else:?>
    <i class="fa fa-angle-up"></i>
    <?php endif;?>
</a>
<?php endif;?>
<?php wp_footer(); ?>
</body>




</html>
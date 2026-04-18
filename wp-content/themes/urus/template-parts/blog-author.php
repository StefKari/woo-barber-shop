<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$author_bio = Urus_Helper::get_option('author_bio',0);
if($author_bio == 0) return;
$description = get_the_author_meta( 'description' );
$twitter = get_the_author_meta('twitter');
$facebook = get_the_author_meta('facebook');
$gplus = get_the_author_meta('gplus');

if ( $description != "" ): ?>
    <div class="about-me">
        <h6 class="author-title"><?php esc_html_e( 'About author', 'urus' ); ?></h6>
        <div class="avatar-img">
            <?php echo get_avatar( get_the_author_meta( 'email' ), '180' ); ?>
        </div>
        <div class="about-text">
            <div class="author-info">
                <h3 class="author-name"><?php the_author(); ?></h3>
            </div>
            <div class="author-desc"><?php the_author_meta( 'description' ); ?></div>
            <?php if ($twitter!='' || $facebook!='' || $gplus!=""):?>
                <div class="author-socials">
                    <a href="<?php echo esc_url($facebook);?>"><i class="fa fa-facebook"></i></a>
                    <a href="<?php echo esc_url($twitter);?>"><i class="fa fa-twitter"></i></a>
                    <a href="<?php echo esc_url($gplus);?>"><i class="fa fa-google-plus"></i></a>
                </div>
            <?php endif;?>
        </div>
    </div>
<?php
endif; ?>
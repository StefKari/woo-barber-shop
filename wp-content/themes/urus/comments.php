<?php
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}
$comment_field     = '<p class="comment-form-comment"><textarea placeholder="' . esc_attr__( 'Comment', 'urus' ) . '" class="input-form" id="comment" name="comment" cols="45" rows="8" aria-required="true">' .
    '</textarea></p>';
$fields            = array(
    'author' => '<div class="row"><div class="col-xs-12 col-sm-6"><p><input placeholder="' . esc_attr__( 'Name *', 'urus' ) . '" type="text" name="author" id="name" class="input-form" /></p></div>',
    'email'  => '<div class="col-xs-12 col-sm-6"><p><input placeholder="' . esc_attr__( 'Email *', 'urus' ) . '" type="text" name="email" id="email" class="input-form" /></p></div></div><!-- /.row -->',
    'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"/>' .
        '<label for="wp-comment-cookies-consent">' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.' ,'urus') . '</label></p>',
);
$comment_form_args = array(
    'class_submit'  => 'button',
    'comment_field' => $comment_field,
    'fields'        => $fields,
);

?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ( '1' === $comments_number ) {
                /* translators: %s: post title */
                printf( _x( 'One thought on &ldquo;%s&rdquo;', 'comments title', 'urus' ), get_the_title() );
            } else {
                printf(
                /* translators: 1: number of comments, 2: post title */
                    _nx(
                        '%1$s thought on &ldquo;%2$s&rdquo;',
                        '%1$s thoughts on &ldquo;%2$s&rdquo;',
                        $comments_number,
                        'comments title',
                        'urus'
                    ),
                    number_format_i18n( $comments_number ),
                    get_the_title()
                );
            }
            ?>
        </h2>


        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 90,
                'callback' => 'Urus::comment_callback'
            ) );
            ?>
        </ol><!-- .comment-list -->

        <?php Urus_Helper::comment_nav(); ?>

    <?php endif; // have_comments() ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
        <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'urus' ); ?></p>
    <?php endif; ?>

    <?php comment_form($comment_form_args); ?>

</div><!-- .comments-area -->

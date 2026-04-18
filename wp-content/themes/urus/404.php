<?php get_header(); ?>

    <div id="primary" class="content-area container">
        <main id="main" class="site-main" role="main">

            <section class="error-404 not-found text-center">
                <header class="page-header">
                    <h1 class="title-404"><?php esc_html_e('404','urus');?></h1>
                    <h2 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'urus' ); ?></h2>
                </header><!-- .page-header -->

                <div class="page-content">
                    <p class="sub-title"><?php esc_html_e( 'THE PAGE YOU ARE LOOKING FOR DOES NOT EXITS', 'urus' ); ?></p>
                    <p class="sub-link"><?php esc_html_e('Please return to','urus');?> <a href="<?php echo esc_url(get_home_url('/'));?>"><?php esc_html_e('Home page','urus');?></a> </p>
                    <?php get_search_form(); ?>
                </div><!-- .page-content -->
            </section><!-- .error-404 -->

        </main><!-- .site-main -->
    </div><!-- .content-area -->

<?php get_footer(); ?>
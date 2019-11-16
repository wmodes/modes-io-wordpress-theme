<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

				<?php if ( have_posts() ) : ?>

                    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
                    <?php /* Create a loop counter to keep track */ ?>
                    <?php $loop_counter = 1; ?>
                    <?php /* If we're on page 1... */ ?>
                    <?php if ( $paged == 1 ): ?>

                        <div class="feature-box">

                        <?php /* Loop through features */ ?>
    					<?php while ( have_posts() and $loop_counter <= 5 ) : the_post(); ?>

                            <div class="postbox">

        						<?php
        						/*
        						 * Include the Post-Format-specific template for the content.
        						 * If you want to override this in a child theme, then include a file
        						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
        						 */
        						get_template_part( 'loop-templates/content', get_post_format() );
        						?>

                            </div>

                            <?php $loop_counter++; ?>
    					<?php endwhile; ?>

                        </div> <!-- feature-box -->

                        <header class="page-header">
                            <?php
                                the_archive_title( '<h1 class="page-title">', '</h1>' );
                                the_archive_description( '<div class="taxonomy-description">', '</div>' );
                            ?>
                        </header><!-- .page-header -->

                    <?php endif; ?>

                    <?php if ( $wp_query->post_count > 5 ) : ?>

                        <div class="normie-box">

                        <?php /* Go through rest of posts */ ?>
                        <?php while ( have_posts() ) : the_post(); ?>

                            <div class="postbox">

                                <?php
                                /*
                                 * Include the Post-Format-specific template for the content.
                                 * If you want to override this in a child theme, then include a file
                                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                                 */
                                get_template_part( 'loop-templates/content', get_post_format() );
                                ?>

                            </div>

                        <?php endwhile; ?>

                            <div class="filler"></div>
                            <div class="filler"></div>
                        </div> <!-- normie-box -->

                    <?php endif; ?>

				<?php else : ?>

					<?php get_template_part( 'loop-templates/content', 'none' ); ?>

				<?php endif; ?>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php understrap_pagination(); ?>

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div> <!-- .row -->

	</div><!-- #content -->

	</div><!-- #archive-wrapper -->

<?php get_footer(); ?>

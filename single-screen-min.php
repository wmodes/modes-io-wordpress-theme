<?php
/*
 * Template Name: Screen With Min Header
 * Template Post Type: post, page
 */
  
get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="single-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<!-- <div class="row"> -->

			<!-- <main class="site-main" id="main"> -->

				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						$ext_url = get_post_meta( $post->ID, 'url', true );
						if (strpos($ext_url, "http") !== 0) {
							$ext_url = get_site_url() . $ext_url;
						}
					?>

					<div class="frame-wrapper embed-responsive">
						<iframe src="<?php echo $ext_url ?>"></iframe>
					</div>

					<?php // get_template_part( 'loop-templates/content', 'single' ); ?>

					<?php // understrap_post_nav(); ?>

				<?php endwhile; // end of the loop. ?>

			<!-- </main> --><!-- #main -->

		<!-- </div> --><!-- .row -->

	</div><!-- #content -->

</div><!-- #single-wrapper -->

<?php get_footer(); ?>

<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php
		the_title(
			sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
			'</a></h2>'
		);
		?>

		<?php if ( 'post' == get_post_type() ) : ?>

			<div class="entry-meta">
				<?php understrap_posted_on(); ?>
			</div> <!-- .entry-meta -->

		<?php endif; ?>

	</header><!-- .entry-header -->

	<?php if ( has_post_thumbnail() ):
		echo get_the_post_thumbnail( $post->ID, 'large' );
	else:
		if ( !function_exists( 'list_files' ) ) { 
		    require_once ABSPATH . '/wp-admin/includes/file.php'; 
		} 
		$bkgd_dir = trailingslashit( get_stylesheet_directory() ) . 'img/bkgd/';
		$files = list_files( $bkgd_dir, 1 );
		$rfile =  basename($files[array_rand($files)]);
		$img_url = trailingslashit( get_stylesheet_directory_uri() ) . 'img/bkgd/' . $rfile;
		echo '<img src="' . $img_url . '" alt="" />';
	endif; ?>

	<div class="entry-content">

		<a href="<?php the_permalink() ?>"><div class="post_excerpt"><?php the_excerpt(); ?></div></a>

		<?php
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'understrap' ),
				'after'  => '</div>',
			)
		);
		?>

	</div><!-- .entry-content -->

</article><!-- #post-## -->

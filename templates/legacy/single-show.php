<?php
/**
 * The Template for displaying all single playlist posts.  Based on TwentyEleven.
 */

get_header();

// 2.3.0: moved show meta to content filter

?>

<div>
	<div id="content" role="main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">

					<!-- custom show output -->

					<?php the_content(); ?>

					<!-- /custom show output -->

					<?php
					wp_link_pages(
						array(
							'before' => '<div class="page-link"><span>' . esc_html( __( 'Pages:', 'radio-station' ) ) . '</span>',
							'after'  => '</div>',
						)
					);
					?>
				</div><!-- .entry-content -->
			</article>

			<?php comments_template( '', true ); ?>

		<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>

<?php
/**
 * Template Name: Show Blog Archive
 * Description: A Page Template that displays an archive of show blog posts
 */

get_header(); ?>


		<section>
			<div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<?php
				// sanitize show ID value
				$show_id = isset( $_GET['show_id'] ) ? absint( $_GET['show_id'] ) : false;
				?>
				<header class="page-header">
					<h1 class="page-title"><?php echo esc_html( get_the_title( $show_id ) ); ?> <?php esc_html_e( 'Blog Archive', 'radio-station' ); ?></h1>
				</header>
				<?php // this is the important part... be careful when you're changing this ?>
				<?php

				// filter to allow for custom show-related post types
				$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );

				// since this is a custom query, we have to do a little trickery to get pagination to work
				$args          = array(
					'post_type'      => $post_types,
					'posts_per_page' => 10,
					'orderby'        => 'post_date',
					'order'          => 'desc',
					'meta_query'     => array(
						array(
							'key'   => 'post_showblog_id',
							'value' => $show_id,
						),
					),
					'paged'          => $paged,
				);
				$archive_query = new WP_Query( $args );
				while ( $archive_query->have_posts() ) :
					$archive_query->the_post();
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<h1 class="entry-title"><a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title( get_the_ID() ) ); ?></a></h1>

							<div class="show-date-and author">
								<?php echo esc_html( get_the_date( get_option( 'date_format' ), get_the_ID() ) ); ?> -
								<?php esc_html_e( 'Posted by', 'radio-station' ); ?> <?php the_author_posts_link(); ?>
							</div>
						</header><!-- .entry-header -->

						<div class="entry-summary">
							<?php the_excerpt(); ?>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>

				<div class="navigation">
				  <div class="alignleft"><?php next_posts_link( '&laquo; Older' ); ?></div>
				  <div class="alignright"><?php previous_posts_link( 'Newer &raquo;' ); ?></div>
				</div>
				<!-- /end important part -->

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'radio-station' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'radio-station' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>

			</div><!-- #content -->
		</section><!-- #primary -->

<?php get_footer(); ?>

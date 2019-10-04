<?php
/**
 * Template Name: Playlist Archive
 * Description: A Page Template that displays an archive of playlists
 */

get_header(); ?>


		<section>
			<div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Playlist Archive', 'radio-station' ); ?></h1>
				</header>
				<?php //this is the important part... be careful when you're changing this ?>
				<?php
					//since this is a custom query, we have to do a little trickery to get pagination to work
				$args = array(
					'post_type'      => 'playlist',
					'posts_per_page' => 10,
					'orderby'        => 'post_date',
					'order'          => 'desc',
					'paged'          => $paged,
					'post_status'    => 'publish',
				);
				$loop = new WP_Query( $args );
				?>
				<?php
				while ( $loop->have_posts() ) :
					$loop->the_post();
					?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
							<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

							<div class="playlist-date-and-show">
								<?php
									$show_id = get_post_meta( get_the_ID(), 'playlist_show_id', true );
								if ( $show_id ) {
									echo esc_html( get_the_title( $show_id ) );
								}
								?>
								 -
								<?php echo esc_html( date( 'M. d, Y', strtotime( $post->post_date ) ) ); ?>
							</div>
					</header><!-- .entry-header -->
				</article>
				<?php endwhile; ?>

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

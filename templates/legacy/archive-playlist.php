<?php
/**
 * The template for displaying Playlist Archive pages.
 */

get_header(); ?>

		<section>
			<div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<?php
				// sanitize show_id
				$show_id = absint( $_GET['show_id'] );
				?>
				<header class="page-header">
					<h1 class="page-title">
						<?php esc_html_e( 'Playlist Archive for', 'radio-station' ); ?> <?php echo esc_html( get_the_title( $show_id ) ); ?>
					</h1>
				</header>

				<!-- custom output : This portion can be edited or inserted into your own theme files -->
				<?php
				$args = array(
					'post_type'      => 'playlist',
					'posts_per_page' => 10,
					'orderby'        => 'post_date',
					'order'          => 'desc',
					'meta_query'     => array(
						array(
							'key'   => 'playlist_show_id',
							'value' => $show_id,
						),
					),
					'paged'          => $paged,
				);
				$loop = new WP_Query( $args );
				// query_posts( $query_string.'&post_type=playlist&orderby=post_date&order=desc&posts_per_page=5&meta_key=playlist_show_id&meta_value='.$show_id );
				?>
				<?php
				while ( $loop->have_posts() ) :
					$loop->the_post();
					?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

				<?php endwhile; ?>

				<nav id="page-nav">
					<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'radio-station' ); ?></h3>
					<div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&larr;</span> ' . esc_html( __( 'Older posts', 'radio-station' ) ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( esc_html( __( 'Newer posts', 'radio-station' ) ) . ' <span class="meta-nav">&rarr;</span>' ); ?></div>
				</nav>

				<!-- end of custom output : This portion can be edited or inserted into your own theme files -->

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

<?php get_sidebar(); ?>
<?php get_footer(); ?>

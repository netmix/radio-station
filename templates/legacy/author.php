<?php
/**
 * The template for displaying DJ info in the Author Archive pages.  Based on the TwentyEleven theme.
 */

get_header();
?>

<?php
	$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
?>

		<section>
			<div id="content" role="main">

			<?php // 2.2.8: remove strict in_array checking
			if ( in_array( 'dj', $curauth->roles ) ) : ?>

				<header class="page-header">
					<h1 class="page-title author"><?php echo esc_html( $curauth->display_name ); ?></h1>
				</header>

				<?php $avatar_size = apply_filters( 'radio_station_dj_avatar_size', 50 ); ?>
				<div id="author-avatar"><?php echo get_avatar( $curauth->ID, $avatar_size ); ?></div>
				<div id="author-description">

					<?php echo wp_kses_post( $curauth->description ); ?>

					<div class="dj-meta">
						URL: <a href="<?php echo esc_url( $curauth->user_url ); ?>"><?php echo esc_url( $curauth->user_url ); ?></a><br />
						Email: <?php echo sanitize_email( $curauth->user_email ); ?><br />
						AIM: <?php echo esc_html( $curauth->aim ); ?><br />
						Jabber: <?php echo esc_html( $curauth->jabber ); ?><br />
						YIM: <?php echo esc_html( $curauth->yim ); ?><br />
					</div>

				</div>

			<?php else : ?>

				<?php if ( have_posts() ) : ?>

					<?php
						/* Queue the first post, that way we know
						 * what author we're dealing with (if that is the case).
						 *
						 * We reset this later so we can run the loop
						 * properly with a call to rewind_posts().
						 */
						the_post();
					?>

					<header class="page-header">
						<h1 class="page-title author"><?php printf( esc_html__( 'Author Archives: %s', 'radio-station' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
					</header>

					<?php
						/* Since we called the_post() above, we need to
						 * rewind the loop back to the beginning that way
						 * we can run the loop properly, in full.
						 */
						rewind_posts();
					?>

					<?php
					// If a user has filled out their description, show a bio on their entries.
					if ( get_the_author_meta( 'description' ) ) :
						?>
					<div id="author-info">
						<div id="author-avatar">
                            <?php $avatar_size = apply_filters( 'radio_station_author_page_avatar_size', 'thumbnail', get_the_ID() ); ?>
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), $avatar_size ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php printf( esc_html__( 'About %s', 'radio-station' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
						</div><!-- #author-description	-->
					</div><!-- #entry-author-info -->
					<?php endif; ?>

					<?php /* Start the Loop */ ?>
					<?php
					while ( have_posts() ) :
						the_post();
						?>

						<?php
							/* Include the Post-Format-specific template for the content.
							 * If you want to overload this in a child theme then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'content', get_post_format() );
						?>

					<?php endwhile; ?>

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

			<?php endif; ?>
			</div><!-- #content -->
		</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

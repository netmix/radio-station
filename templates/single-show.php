<?php
/**
 * The Template for displaying all single playlist posts.  Based on TwentyEleven.
 */

get_header();
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

					<?php // custom show output : This portion can be edited or inserted into your own theme files ?>
					<div class="alignleft">
						<h3><?php esc_html_e( 'Hosted by', 'radio-station' ); ?>:</h3>
						<?php
						$djs   = get_post_meta( get_the_ID(), 'show_user_list', true );
						$count = 0;
						if ( $djs ) {
							foreach ( $djs as $dj ) {
								$count++;
								$user_info = get_userdata( $dj );

								echo '<a href="' . esc_url( get_author_posts_url( $dj ) ) . '">' . esc_html( $user_info->display_name ) . '</a>';

								$dj_count = count( $djs );
								if ( ( 1 === $count && 2 === $dj_count ) || ( $dj_count > 2 && $count === $dj_count - 1 ) ) {
									echo ' and ';
								} elseif ( ( $count < count( $djs ) ) && ( count( $djs ) > 2 ) ) {
									echo ', ';
								}
							}
						}
						?>
					</div>

					<div class="station-genres alignright">
						<h3><?php esc_html_e( 'Genre', 'radio-station' ); ?>:</h3>
						<?php
						// use this function instead if you would like the genres to link to an archive page
						// wp_list_categories( array('taxonomy' => 'genres', 'title_li' => '') );
						?>
						<ul>
							<?php
							$terms = wp_get_post_terms( get_the_ID(), 'genres' );
							foreach ( $terms as $genre ) {
								echo '<li>' . esc_html( $genre->name ) . '</li>';
							}
							?>
						</ul>
					</div>

					<div style="clear:both;"><hr /></div>

					<div class="station-featured-image alignright">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'medium' );
						}
						$show_email = get_post_meta( get_the_ID(), 'show_email', true );
						if ( $show_email ) {
							?>
							<p class="station-dj-email">
								<a href="mailto:<?php echo sanitize_email( $show_email ); ?>">
									<?php esc_html_e( 'Email the DJ', 'radio-station' ); ?>
								</a>
							</p>
							<?php
						}
						?>

						<?php
						$show_link = get_post_meta( get_the_ID(), 'show_link', true );
						if ( $show_link ) {
							?>
							<p class="station-show-link">
								<a href="<?php echo esc_url( $show_link ); ?>">
									<?php esc_html_e( 'Show Website', 'radio-station' ); ?>
								</a>
							</p>
							<?php
						}
						?>
					</div>

					<?php the_content(); ?>

					<div class="station-broadcast-file">
						<a href="<?php echo esc_url( get_post_meta( get_the_ID(), 'show_file', true ) ); ?>">
							<?php esc_html_e( 'Most recent broadcast', 'radio-station' ); ?>
						</a>
					</div>

					<div class="station-show-schedules">
						<h3><?php esc_html_e( 'Schedule', 'radio-station' ); ?></h3>
						<ul>
							<?php
							// 12-hour time
							$shifts = get_post_meta( get_the_ID(), 'show_sched', true );
							if ( $shifts ) {
								foreach ( $shifts as $shift ) {
									$weekday = radio_station_translate_weekday( $shift['day'] );
									echo '<li>';
									echo esc_html( $weekday . ' - ' . $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'] . ' - ' . $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'] );
									echo '</li>';
								}
							}

							// 24-hour time
							/*
							$shifts = get_post_meta( get_the_ID(), 'show_sched', true );
							if ( $shifts ) {
							foreach ( $shifts as $shift ) {
							$start = date('H:i', strtotime('1981-04-28 '.$shift['start_hour'].':'.$shift['start_min'].':00 '));
							$end = date('H:i', strtotime('1981-04-28 '.$shift['end_hour'].':'.$shift['end_min'].':00 '));

							$weekday = radio_station_translate_weekday( $shift['day'] );
							echo '<li>';
							echo $weekday.' - '.$start.' - '.$end;
							echo '</li>';
							}
							}
							*/
							?>
						</ul>
					</div>

					<div class="station-show-playlists">
						<h3><?php esc_html_e( 'Playlists', 'radio-station' ); ?></h3>
						<?php echo do_shortcode( '[get-playlists show="' . get_the_ID() . '" limit="5"]' ); ?>
					</div>

					<?php echo wp_kses_post( radio_station_myplaylist_get_posts_for_show( get_the_ID(), __( 'Blog Posts', 'radio-station' ), '10' ) ); ?>

					<!-- /custom show output -->

					<?php
					wp_link_pages(
						array(
							'before' => '<div class="page-link"><span>' . __( 'Pages:', 'radio-station' ) . '</span>',
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

<?php

// --- get the playlist data ---
$playlist = get_post_meta( get_the_ID(), 'playlist', true );

if ( $playlist ) : ?>
	<div class="myplaylist-playlist-entries">
		<table>
		<tr>
			<th>#</th>
			<th><?php esc_html_e( 'Artist', 'radio-station' ); ?></th>
			<th><?php esc_html_e( 'Song', 'radio-station' ); ?></th>
			<th><?php esc_html_e( 'Album', 'radio-station' ); ?></th>
			<th><?php esc_html_e( 'Record Label', 'radio-station' ); ?></th>
			<th><?php esc_html_e( 'Comments', 'radio-station' ); ?></th>
		</tr>
		<?php $count = 1;
		foreach ( $playlist as $entry ) : ?>
			<?php if ( 'played' === $entry['playlist_entry_status'] ) : ?>
				<?php $new_class = '';
				if ( isset( $entry['playlist_entry_new'] ) && 'on' === $entry['playlist_entry_new'] ) {
					$new_class = 'class="new"';}
				?>
				<tr <?php echo $new_class; ?>>
					<td><?php echo $count; ?></td>
					<td><?php echo esc_html( $entry['playlist_entry_artist'] ); ?></td>
					<td><?php echo esc_html( $entry['playlist_entry_song'] ); ?></td>
					<td><?php echo esc_html( $entry['playlist_entry_album'] ); ?></td>
					<td><?php echo esc_html( $entry['playlist_entry_label'] ); ?></td>
					<td><?php echo esc_html( $entry['playlist_entry_comments'] ); ?></td>
				</tr>
			<?php $count++; endif; ?>
		<?php endforeach; ?>
		</table>
	</div>
	<? else: ?>
	<div class="myplaylist-no-entries">
		<?php esc_html_e( 'No entries for this Playlist', 'radio-station' ); ?>
	</div>
<?php endif; ?>

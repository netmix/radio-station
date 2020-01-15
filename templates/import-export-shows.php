<div style="width: 620px; padding: 10px">

	<h2><?php esc_html_e( 'Import/Export All Show Data', 'radio-station' ); ?></h2>
	<!-- <div class="metabox-holder" style="display: flex; border: 1px solid red; width: 900px;"> -->
	<div class="metabox-holder" style="display: flex;">
	  <div class="postbox" style="width: 50%; padding: 10px; margin-right: 10px;">
			<h2><?php esc_html_e( 'Import Show Data', 'radio-station' ); ?></h2>
	    <div class="inside">
	      <p><?php
				   _e( 'Import and replace all shows and show metadata from a YAML file.', 'radio-station' );
					 echo '&nbsp;</p><p>';
					 _e( '<span style="color: red;"><strong>WARNING</strong></span>, this will delete everything you have currently configured. We strongly suggest exporting a backup first.');
					 ?></p>
	      <form method="post" enctype="multipart/form-data">
					&nbsp;
	        <p>
	          <input type="file" name="import_file"/>
	        </p>
					&nbsp;
	        <p>
	          <input type="hidden" name="action" value="radio_station_yaml_import_action" />
	          <?php wp_nonce_field( 'yaml_import_nonce', 'yaml_import_nonce' ); ?>
	          <?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
	        </p>
	      </form>
	    </div><!-- .inside -->
	  </div><!-- .postbox -->
	  <!-- <div class="postbox" style="padding: 10px; flex-grow:1; margin-left: 10px;"> -->
	  <div class="postbox" style="padding: 10px; width: 50%; margin-left: 10px;">
			<h2><?php esc_html_e( 'Export Show Data', 'radio-station' ); ?></h2>
			<div class="inside">
				FIXME
			</div>
		</div>
	</div><!-- first .metabox-holder -->

	<div id="error-log-div">
		<?php
		//pull in any parsing error details for display to the user
		global $yaml_parse_errors;
		echo '<pre>';
		echo wordwrap($yaml_parse_errors, 85);
		echo '</pre>';
		?>
	</div>

</div>

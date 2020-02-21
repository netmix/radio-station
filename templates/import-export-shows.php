<?php
/*
 * Import/Export Show admin screen template
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */
 ?>

<div style="width: 620px; padding: 10px">

	<h2><?php esc_html_e( 'Import/Export All Show Data', 'radio-station' ); ?></h2>
	<!-- <div class="metabox-holder" style="display: flex; border: 1px solid red; width: 900px;"> -->
	<div class="metabox-holder" style="display: flex;">
	  <div class="postbox" style="width: 50%; padding: 10px; margin-right: 10px;">
			<h2><?php esc_html_e( 'Import Show Data', 'radio-station' ); ?></h2>
	    <div class="inside">
	      <form method="post" enctype="multipart/form-data">
          <div style="height:175px;">
    	     <p>
           <?php
				   _e( 'Import shows and show metadata from a YAML file.', 'radio-station' );
					 ?>
           &nbsp;</p>
           <input type="hidden" value="0" name="delete_show_data" onclick=check()>
           <input id="delete-data-checkbox" type="checkbox" value="1" name="delete_show_data" onclick=check()> Delete existing show data </input>
           <p id="delete-data-warning" style="display: none;">
           <?php
					 _e( '<span style="color: red;"><strong>WARNING</strong></span>, this will delete everything you have currently configured. We strongly suggest exporting a backup first.');
					 ?>
           </p>
          </div>
  					&nbsp;
  	        <p>
  	          <input type="file" name="import_file"/>
  	        </p>
  					&nbsp;
	          <input type="hidden" name="action" value="radio_station_yaml_import_action" />
	          <?php wp_nonce_field( 'yaml_import_nonce', 'yaml_import_nonce' ); ?>
	          <?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
	      </form>
	    </div><!-- .inside -->
	  </div><!-- .postbox -->
	  <!-- <div class="postbox" style="padding: 10px; flex-grow:1; margin-left: 10px;"> -->
	  <div class="postbox" style="padding: 10px; width: 50%; margin-left: 10px;">
			<h2><?php esc_html_e( 'Export Show Data', 'radio-station' ); ?></h2>
			<div class="inside">
	      <form method="post" enctype="multipart/form-data">
          <div style="height: 247px;">
           <p><?php
				   _e( 'Export all show data to a downloadable file.', 'radio-station' );
					 echo '&nbsp;</p><p>';
					 ?></p>
  					&nbsp;
  	        <p>
  	          <input type="text" name="export_file_name" size="27" placeholder="Optional download file name"/>
  	        </p><p>
  	          <input type="text" name="image_prefix_url" size="27" placeholder="Optional image location URL"/>
            </p>
  					&nbsp;
	          <input type="hidden" name="action" value="radio_station_yaml_export_action" />
	          <?php wp_nonce_field( 'yaml_export_nonce', 'yaml_export_nonce' ); ?>
          </div>
	          <?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
	      </form>
			</div>
		</div>
	</div><!-- first .metabox-holder -->

	<div id="error-log-div" style="width:100%;">
		<?php
		//pull in any parsing error details for display to the user
		global $yaml_parse_errors;
		// echo '<pre>';
		// echo wordwrap($yaml_parse_errors, 85);
		echo $yaml_parse_errors;
		// echo '</pre>';

		?>
	</div>

</div>
